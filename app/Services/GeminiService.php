<?php

namespace App\Services;

use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Product;
use App\Models\Region;
use App\Models\Status;
use App\Models\Type;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeminiService
{
    private string $apiKey;
    private string $endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function chatWithFile(array $history, string $message, string $base64Data, string $mimeType): string
    {
        $priceCtx = $this->priceContext();

        $sysPrompt = <<<PROMPT
Siz ChorvaAI — chorva mollari mutaxassisi va baholovchisisiz. Rasmlarni ko'rib tahlil qila olasiz.

Chorva molining rasmini ko'rganda ALBATTA quyidagilarni bering:
1. **Zoti** — ko'rinish belgilari asosida
2. **Taxminiy yoshi** — tana o'lchami, shox, yelini asosida
3. **Taxminiy vazni** — gavda tuzilishi, muskulatura asosida (kg)
4. **Badan holati (BCS)** — 1-9 shkala
5. **Taxminiy bozor bahosi** — quyidagi narxlar asosida (so'mda):
{$priceCtx}
6. **Tavsiya** — sotish/parvarish bo'yicha maslahat

Foydalanuvchi tilida javob bering (o'zbek/rus/ingliz).
PROMPT;

        $contents = [];
        $contents[] = [
            'role'  => 'user',
            'parts' => [
                ['text' => $message],
                ['inline_data' => ['mime_type' => $mimeType, 'data' => $base64Data]],
            ],
        ];

        $response = Http::timeout(60)->post("{$this->endpoint}?key={$this->apiKey}", [
            'system_instruction' => ['parts' => [['text' => $sysPrompt]]],
            'contents'           => $contents,
            'generationConfig'   => ['maxOutputTokens' => 8192, 'temperature' => 0.7],
        ]);

        if ($response->failed()) {
            \Log::error('Gemini file API error', ['status' => $response->status(), 'body' => $response->json()]);
            return "Kechirasiz, faylni tahlil qilishda muammo yuz berdi.";
        }

        $json   = $response->json();
        $text   = data_get($json, 'candidates.0.content.parts.0.text');
        $finish = data_get($json, 'candidates.0.finishReason');

        \Log::info('Gemini file response', ['finish' => $finish, 'len' => strlen($text ?? '')]);

        if (! $text) {
            \Log::warning('Gemini file no text', ['finish' => $finish, 'json' => $json]);
            return "Kechirasiz, javob ololmadim.";
        }

        return $text;
    }

    private function priceContext(): string
    {
        return Cache::remember('gemini_price_context', 1800, function () {
            $stats = \App\Models\Product::selectRaw('
                COUNT(*) as total,
                MIN(price) as min_price,
                MAX(price) as max_price,
                ROUND(AVG(price)) as avg_price
            ')->first();

            $top = \App\Models\Product::select('name','price')
                ->orderByDesc('views_count')->limit(5)->get()
                ->map(fn($p) => "- {$p->name}: {$p->price} so'm")
                ->join("\n");

            return "Minimal: {$stats->min_price} so'm | Maksimal: {$stats->max_price} so'm | O'rtacha: {$stats->avg_price} so'm\n{$top}";
        });
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
        $ctx = $this->platformContext();

        return <<<PROMPT
Siz ChorvaAI — O'zbekistondagi chorva mollar marketplace platformasining AI yordamchisisiz. Siz matn VA rasmlarni tahlil qila olasiz.

Foydalanuvchilarga quyidagi mavzularda yordam bering:
- Chorva mollari: sigir, qo'y, echki, ot, tuya, cho'chqa, parrandalar
- Narxlar va bozor holati haqida ma'lumot
- Chorva parvarishlash: oziqlanish, emlash, gigiena
- Kasalliklar va davolash usullari
- Sotib olish va sotish bo'yicha maslahatlar
- Zot tanlash va nasl yaxshilash
- ChorvaAI platformasida qanday foydalanish

=== RASM TAHLIL QILISH (MUHIM) ===
Foydalanuvchi chorva molining rasmini yuborganda ALBATTA quyidagilarni baholang:

1. **Zoti** — rasmdan ko'rinadigan belgilar asosida zotini aniqlang (Holshteyn, Simmental, Qoraqo'l, mahalliy zot va h.k.)
2. **Taxminiy yoshi** — tana o'lchami, shox holati, tish belgilari (ko'rinsa), umumiy ko'rinish asosida
3. **Taxminiy vazni** — gavda tuzilishi, orqa-ko'krak kenglik, dumba muskulatura asosida (kg da)
4. **Badan holati (BCS 1-9)** — semirishlik darajasi, qovurg'alar ko'rinishi, dumba to'liqligi
5. **Taxminiy bozor bahosi** — yuqoridagi platforma ma'lumotlaridagi narxlar bilan solishtiring va o'zbek so'mida diapazon bering
6. **Sotish maslahati** — yaxshi tomonlari va kamchiliklari, narxni oshirish yo'llari

Agar rasm sifati past bo'lsa yoki aniq ko'rinmasa — ko'ringan belgilar asosida taxmin qiling va noaniqligini ayting.
Hech qachon "rasmni ko'ra olmayman" demang — har doim ko'ringan narsalar asosida baholashga harakat qiling.

=== PLATFORMA MA'LUMOTLARI (faqat o'qish uchun) ===
{$ctx}
=== MA'LUMOTLAR TUGADI ===

Qoidalar:
- Yuqoridagi ma'lumotlardan foydalanib aniq javob bering
- Foydalanuvchi "qanday kategoriyalar bor", "qaysi viloyatlar bor", "narxi qancha" deb so'rasa, yuqoridagi haqiqiy ma'lumotlarni ko'rsating
- Javoblaringiz aniq va foydali bo'lsin
- Foydalanuvchi qaysi tilda yozsa, o'sha tilda javob bering (o'zbek, rus yoki ingliz)
- Doktor yoki veterinar maslahatiga muhtoj bo'lgan jiddiy holatlarda mutaxassisga murojaat qilishni tavsiya eting
PROMPT;
    }

    private function platformContext(): string
    {
        // 30 daqiqa kesh — bazaga har so'rovda ulanmaslik uchun
        return Cache::remember('gemini_platform_context', 1800, function () {
            $categories = Category::select('id', 'name', 'parent_id')
                ->orderBy('parent_id')
                ->get()
                ->map(fn($c) => ($c->parent_id ? '  └ ' : '') . $c->name)
                ->join("\n");

            $regions = Region::select('name')->pluck('name')->join(', ');

            $cities = City::select('name', 'region_id')
                ->with('region:id,name')
                ->get()
                ->groupBy(fn($c) => $c->region?->name ?? 'Boshqa')
                ->map(fn($cities, $region) => "$region: " . $cities->pluck('name')->join(', '))
                ->join("\n");

            $types   = Type::select('name')->pluck('name')->join(', ');
            $colors  = Color::select('name')->pluck('name')->join(', ');
            $statuses = Status::select('name')->pluck('name')->join(', ');

            $stats = Product::selectRaw('
                COUNT(*) as total,
                MIN(price) as min_price,
                MAX(price) as max_price,
                ROUND(AVG(price)) as avg_price
            ')->first();

            $topProducts = Product::select('name', 'price')
                ->where('status_id', Status::where('name', 'like', '%aktiv%')->value('id'))
                ->orderByDesc('views_count')
                ->limit(10)
                ->get()
                ->map(fn($p) => "- {$p->name}: {$p->price} so'm")
                ->join("\n");

            return <<<CTX
Kategoriyalar:
{$categories}

Viloyatlar: {$regions}

Shaharlar viloyatlar bo'yicha:
{$cities}

Hayvon turlari: {$types}
Ranglar: {$colors}
E'lon statuslari: {$statuses}

Platforma statistikasi:
- Jami e'lonlar soni: {$stats->total} ta
- Minimal narx: {$stats->min_price} so'm
- Maksimal narx: {$stats->max_price} so'm
- O'rtacha narx: {$stats->avg_price} so'm

Eng ko'p ko'rilgan aktiv e'lonlar:
{$topProducts}
CTX;
        });
    }
}
