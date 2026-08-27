<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnthropicService
{
    private const API_URL = 'https://api.anthropic.com/v1/messages';
    private const MAX_ITERATIONS = 8;

    public function __construct(private MarketAnalysisTools $tools) {}

    /**
     * Send a conversation and return the final assistant text.
     *
     * @param  array  $messages  [['role'=>'user'|'assistant','content'=>string], ...]
     */
    public function chat(array $messages): string
    {
        $apiMessages = array_map(fn($m) => [
            'role'    => $m['role'],
            'content' => $m['content'],
        ], $messages);

        return $this->runLoop($apiMessages);
    }

    private function runLoop(array $messages): string
    {
        for ($i = 0; $i < self::MAX_ITERATIONS; $i++) {
            $response = $this->callApi($messages);

            $stopReason = $response['stop_reason'] ?? 'end_turn';

            if ($stopReason !== 'tool_use') {
                return $this->extractText($response['content'] ?? []);
            }

            // Append assistant message with tool_use blocks
            $messages[] = ['role' => 'assistant', 'content' => $response['content']];

            // Execute each tool call and collect results
            $toolResults = [];
            foreach ($response['content'] as $block) {
                if (($block['type'] ?? '') === 'tool_use') {
                    try {
                        $result = $this->tools->execute($block['name'], $block['input'] ?? []);
                    } catch (\Throwable $e) {
                        Log::warning("Tool '{$block['name']}' failed: " . $e->getMessage());
                        $result = ['error' => $e->getMessage()];
                    }

                    $toolResults[] = [
                        'type'        => 'tool_result',
                        'tool_use_id' => $block['id'],
                        'content'     => json_encode($result, JSON_UNESCAPED_UNICODE),
                    ];
                }
            }

            $messages[] = ['role' => 'user', 'content' => $toolResults];
        }

        return "Kechirasiz, javob olishda muammo yuz berdi. Iltimos, qayta urinib ko'ring.";
    }

    private function callApi(array $messages): array
    {
        $response = Http::withHeaders([
            'x-api-key'         => config('ai.anthropic_key'),
            'anthropic-version' => '2023-06-01',
        ])->timeout(90)->post(self::API_URL, [
            'model'      => config('ai.model'),
            'max_tokens' => config('ai.max_tokens'),
            'system'     => $this->systemPrompt(),
            'messages'   => $messages,
            'tools'      => $this->tools->definitions(),
        ]);

        if ($response->failed()) {
            Log::error('Anthropic API error: ' . $response->body());
            throw new \RuntimeException('AI xizmati vaqtincha mavjud emas.');
        }

        return $response->json();
    }

    private function extractText(array $content): string
    {
        return collect($content)
            ->where('type', 'text')
            ->pluck('text')
            ->implode("\n\n");
    }

    private function systemPrompt(): string
    {
        $now = now()->format('Y-yil, d-F');

        return <<<PROMPT
Sen ChorvaAI — O'zbekistondagi chorva mollar bozorini tahlil qiladigan AI agentsan.
Bugungi sana: $now.

Vazifang:
- Foydalanuvchining savoliga javob berish uchun mavjud toollardan foydalanib bozor ma'lumotlarini ol
- Narxlar, trendlar va viloyatlar bo'yicha aniq raqamli tahlil taqdim et
- Narxlarni "so'm" birligida yozing (masalan: 5 000 000 so'm)
- Har doim O'zbek tilida javob ber
- Javobingni qisqa, aniq va foydali qil
- Agar ma'lumotlar yetarli bo'lmasa yoki e'lonlar soni 0 bo'lsa, foydalanuvchiga xabar ber

Misol savollar:
- "Hozir qoramollarning o'rtacha narxi qancha?"
- "Qaysi viloyatda qo'ylar arzonroq?"
- "So'nggi 3 oyda narxlar o'zgarganmi?"
- "Ot va qoramol narxini solishtir"
PROMPT;
    }
}
