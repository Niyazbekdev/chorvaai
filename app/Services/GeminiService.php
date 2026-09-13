<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private const API_BASE = 'https://generativelanguage.googleapis.com/v1beta/models';
    private const MAX_ITERATIONS = 8;

    public function __construct(private MarketAnalysisTools $tools) {}

    /**
     * @param  array  $messages  [['role'=>'user'|'assistant','content'=>string], ...]
     */
    public function chat(array $messages): string
    {
        $geminiMessages = array_map(fn($m) => [
            'role'  => $m['role'] === 'assistant' ? 'model' : 'user',
            'parts' => [['text' => $m['content']]],
        ], $messages);

        return $this->runLoop($geminiMessages);
    }

    private function runLoop(array $messages): string
    {
        for ($i = 0; $i < self::MAX_ITERATIONS; $i++) {
            $response  = $this->callApi($messages);
            $candidate = $response['candidates'][0] ?? null;

            if (! $candidate) {
                break;
            }

            $parts         = $candidate['content']['parts'] ?? [];
            $functionCalls = array_values(array_filter($parts, fn($p) => isset($p['functionCall'])));

            if (empty($functionCalls)) {
                return $this->extractText($parts);
            }

            // Modelning javobini tarixga qo'shamiz — faqat functionCall va text partlar
            // (thoughtSignature kabi ichki maydonlarni olib tashlaymiz)
            $historyParts = array_values(array_filter(
                $parts,
                fn($p) => isset($p['functionCall']) || isset($p['text'])
            ));

            // PHP json_decode bo'sh {} ni [] ga aylantiradi; qayta yuborganda xato chiqadi.
            // stdClass() ni json_encode {} (object) sifatida kodlaydi.
            foreach ($historyParts as &$part) {
                if (isset($part['functionCall'])) {
                    $args = $part['functionCall']['args'] ?? [];
                    $part['functionCall']['args'] = empty($args) ? new \stdClass() : $args;
                }
            }
            unset($part);

            $messages[] = ['role' => 'model', 'parts' => $historyParts];

            // Toollarni ishlatib natijalarni to'playmiz
            $responses = [];
            foreach ($functionCalls as $part) {
                $fc   = $part['functionCall'];
                $name = $fc['name'];
                $args = $fc['args'] ?? [];

                try {
                    $result = $this->tools->execute($name, $args);
                } catch (\Throwable $e) {
                    Log::warning("Tool '{$name}' failed: " . $e->getMessage());
                    $result = ['error' => $e->getMessage()];
                }

                $responses[] = [
                    'functionResponse' => [
                        'name'     => $name,
                        'response' => ['result' => json_encode($result, JSON_UNESCAPED_UNICODE)],
                    ],
                ];
            }

            $messages[] = ['role' => 'user', 'parts' => $responses];
        }

        return "Kechirasiz, javob olishda muammo yuz berdi. Iltimos, qayta urinib ko'ring.";
    }

    private function callApi(array $messages): array
    {
        $model  = config('ai.model');
        $apiKey = config('ai.gemini_key');
        $url    = self::API_BASE . "/{$model}:generateContent?key={$apiKey}";

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $this->systemPrompt()]],
            ],
            'contents'         => $messages,
            'tools'            => [
                ['function_declarations' => $this->tools->definitions()],
            ],
            'generationConfig' => [
                'maxOutputTokens' => config('ai.max_tokens'),
                'temperature'     => 0.3,
            ],
        ];

        // 429 rate-limit uchun 2 marta qayta urinish
        foreach ([0, 5, 15] as $delay) {
            if ($delay > 0) {
                sleep($delay);
            }

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout(90)
                ->post($url, $payload);

            if ($response->status() === 429) {
                Log::warning('Gemini rate limit, retrying in ' . $delay . 's...');
                continue;
            }

            if ($response->failed()) {
                Log::error('Gemini API error: ' . $response->body());
                throw new \RuntimeException('AI xizmati vaqtincha mavjud emas.');
            }

            return $response->json();
        }

        Log::error('Gemini API rate limit exhausted');
        throw new \RuntimeException('AI xizmati band, iltimos bir daqiqadan so\'ng urinib ko\'ring.');
    }

    private function extractText(array $parts): string
    {
        return collect($parts)
            ->filter(fn($p) => isset($p['text']))
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
- Har doim O'zbek tilida javob ber (agar foydalanuvchi rus tilida yozsa, rus tilida javob ber)
- Javobingni qisqa, aniq va foydali qil
- Agar ma'lumotlar yetarli bo'lmasa yoki e'lonlar soni 0 bo'lsa, foydalanuvchiga xabar ber
- Saytdagi real e'lonlarni ko'rsatish kerak bo'lsa search_products toolidan foydalan

Misol savollar:
- "Hozir qoramollarning o'rtacha narxi qancha?"
- "Qaysi viloyatda qo'ylar arzonroq?"
- "So'nggi 3 oyda narxlar o'zgarganmi?"
- "Toshkentda sotilayotgan qoramollarni ko'rsat"
PROMPT;
    }
}
