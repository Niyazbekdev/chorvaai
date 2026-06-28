<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    private string $apiKey;
    private string $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function chat(array $history, string $newMessage): string
    {
        $contents = [];

        foreach ($history as $msg) {
            $contents[] = [
                'role'  => $msg['role'],
                'parts' => [['text' => $msg['content']]],
            ];
        }

        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $newMessage]],
        ];

        $response = Http::timeout(30)->post("{$this->endpoint}?key={$this->apiKey}", [
            'system_instruction' => [
                'parts' => [['text' => $this->systemPrompt()]],
            ],
            'contents'           => $contents,
            'generationConfig'   => [
                'maxOutputTokens' => 800,
                'temperature'     => 0.7,
            ],
        ]);

        if ($response->failed()) {
            \Log::error('Gemini API error', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return "Kechirasiz, hozir javob berishda muammo yuz berdi. Iltimos, qaytadan urinib ko'ring.";
        }

        return $response->json('candidates.0.content.parts.0.text')
            ?? "Kechirasiz, javob ololmadim. Iltimos, qaytadan urinib ko'ring.";
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
Siz ChorvaAI — O'zbekistondagi chorva mollar marketplace platformasining AI yordamchisisiz.

Foydalanuvchilarga quyidagi mavzularda yordam bering:
- Chorva mollari: sigir, qo'y, echki, ot, tuya, cho'chqa, parrandalar
- Narxlar va bozor holati haqida ma'lumot
- Chorva parvarishlash: oziqlanish, emlash, gigiena
- Kasalliklar va davolash usullari
- Sotib olish va sotish bo'yicha maslahatlar
- Zot tanlash va nasl yaxshilash
- ChorvaAI platformasida qanday foydalanish

Qoidalar:
- Javoblaringiz qisqa, aniq va foydali bo'lsin (3-5 gap)
- Foydalanuvchi qaysi tilda yozsa, o'sha tilda javob bering (o'zbek, rus yoki ingliz)
- Faqat chorvachilik va platforma mavzularida yordam bering
- Doktor yoki veterinar maslahatiga muhtoj bo'lgan jiddiy holatlarda mutaxassisga murojaat qilishni tavsiya eting
PROMPT;
    }
}
