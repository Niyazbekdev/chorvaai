<?php

namespace App\Services;

use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\Region;
use App\Models\Status;
use Illuminate\Support\Collection;

class ProductRetrieverService
{
    private array $animalMap = [
        'sigir'    => ['sigir', 'qoramol', 'buqa', 'buz', 'корова', 'бык', 'буйвол', 'cow', 'bull'],
        'qoy'      => ["qo'y", 'qoy', 'qozy', 'toqli', "qo'zi", 'ovca', 'овца', 'баран', 'sheep', 'lamb', 'ram'],
        'echki'    => ['echki', 'uloq', 'коза', 'goat', 'kid'],
        'ot'       => ['ot', 'biya', 'toy', 'лошадь', 'конь', 'horse', 'mare', 'stallion'],
        'tuya'     => ['tuya', 'верблюд', 'camel'],
        'chochqa'  => ["cho'chqa", 'chochqa', 'donuz', 'свинья', 'pig', 'boar'],
        'tovuq'    => ['tovuq', "xo'roz", "jo'ja", 'joʻja', 'курица', 'петух', 'chicken', 'hen', 'rooster'],
        'ordak'    => ["o'rdak", 'ordak', 'утка', 'duck'],
        'goz'      => ["g'oz", 'goz', 'гусь', 'goose'],
        'quyon'    => ['quyon', 'кролик', 'rabbit'],
    ];

    public function search(string $userQuery, int $limit = 8): Collection
    {
        $q = mb_strtolower(trim($userQuery));

        $priceRange = $this->detectPriceRange($q);
        $regionId   = $this->detectRegionId($q);
        $categoryId = $this->detectCategoryId($q);

        // Birinchi urinish: barcha filterlar bilan
        $results = $this->buildQuery($q, $priceRange, $regionId, $categoryId, $limit);

        // Natija yo'q bo'lsa — matn filterini olib tashlab qayta qidiramiz
        if ($results->isEmpty() && ($regionId || $categoryId)) {
            $results = $this->buildQuery(null, $priceRange, $regionId, $categoryId, $limit);
        }

        // Baribir bo'sh bo'lsa — faqat narx va kategoriya
        if ($results->isEmpty() && $categoryId) {
            $results = $this->buildQuery(null, $priceRange, null, $categoryId, $limit);
        }

        return $results;
    }

    private function buildQuery(
        ?string $textQuery,
        ?array $priceRange,
        ?int $regionId,
        ?int $categoryId,
        int $limit
    ): Collection {
        $builder = Product::query()
            ->with(['category', 'region', 'city', 'status', 'color'])
            ->whereHas('status', fn ($s) => $s->where('name', 'not like', '%sotildi%'));

        if ($priceRange) {
            $builder->whereBetween('price', $priceRange);
        }

        if ($regionId) {
            $builder->where('region_id', $regionId);
        }

        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        if ($textQuery !== null) {
            // O'zbek ko'plik qo'shimchalarini olib tashlash: lar/lar/ni/da/ga
            $stems = $this->stemWords($textQuery);

            if (!empty($stems)) {
                $builder->where(function ($qb) use ($stems) {
                    foreach ($stems as $stem) {
                        $qb->orWhere('name', 'like', "%{$stem}%")
                           ->orWhere('description', 'like', "%{$stem}%");
                    }
                });
            }
        }

        return $builder->orderByDesc('views_count')->limit($limit)->get();
    }

    private function detectPriceRange(string $q): ?array
    {
        if (preg_match('/(\d[\d\s.,]*)\s*(?:million|mln|млн)/i', $q, $m)) {
            $amount = (float) preg_replace('/[\s,]/', '', $m[1]) * 1_000_000;
            return [(int)($amount * 0.7), (int)($amount * 1.3)];
        }
        if (preg_match('/(\d[\d\s.,]*)\s*(?:ming|тысяч|k\b)/i', $q, $m)) {
            $amount = (float) preg_replace('/[\s,]/', '', $m[1]) * 1_000;
            return [(int)($amount * 0.7), (int)($amount * 1.3)];
        }
        return null;
    }

    private function stemWords(string $query): array
    {
        $stopWords = ['bor', 'ham', 'yoki', 'uchun', 'qanday', 'qancha', 'saytda',
                      'bilan', 'narxi', 'narxlari', 'sotib', 'olmoqchi', 'sotmoqchi',
                      'menga', 'bizga', 'xohlaman', 'istaman', 'haqida', 'bormi',
                      'и', 'в', 'на', 'с', 'за', 'по', 'для', 'что', 'как'];

        $words = array_filter(
            preg_split('/[\s,\.!?]+/u', $query),
            fn ($w) => mb_strlen($w) > 2 && !in_array($w, $stopWords)
        );

        // O'zbek/rus ko'plik va kelishik qo'shimchalarini kesish
        $suffixes = ['lardan', 'larni', 'larda', 'larga', 'larmi', 'lar',
                     'ning', 'dan', 'lar', 'ни', 'ов', 'ах', 'am', 'im'];

        $stems = [];
        foreach ($words as $word) {
            $stem = $word;
            foreach ($suffixes as $sfx) {
                if (mb_strlen($word) > mb_strlen($sfx) + 2 && mb_substr($word, -mb_strlen($sfx)) === $sfx) {
                    $stem = mb_substr($word, 0, -mb_strlen($sfx));
                    break;
                }
            }
            // Kalta stem filteri
            if (mb_strlen($stem) >= 3) {
                $stems[] = $stem;
            }
        }

        return array_values(array_unique($stems));
    }

    public function formatForContext(Collection $products): string
    {
        if ($products->isEmpty()) {
            return '';
        }

        $lines = $products->values()->map(function (Product $p, int $i) {
            $parts = [($i + 1) . '. ' . $p->name];

            $parts[] = "Narx: " . number_format($p->price, 0, '.', ' ') . " so'm";

            if ($p->category) {
                $parts[] = "Tur: {$p->category->name}";
            }
            if ($p->region) {
                $loc = $p->region->name;
                if ($p->city) {
                    $loc .= ", {$p->city->name}";
                }
                $parts[] = "Joylashuv: $loc";
            }
            if ($p->color) {
                $parts[] = "Rang: {$p->color->name}";
            }
            if ($p->gender) {
                $parts[] = "Jinsi: {$p->gender}";
            }
            if ($p->age) {
                $parts[] = "Yoshi: {$p->age} oy";
            }
            if ($p->weight) {
                $parts[] = "Vazni: {$p->weight} kg";
            }
            if ($p->contact_phone) {
                $parts[] = "Tel: {$p->contact_phone}";
            }
            if ($p->description) {
                $parts[] = "Tavsif: " . mb_substr($p->description, 0, 120);
            }

            return implode(' | ', $parts);
        });

        return "=== So'rovga mos saytdagi e'lonlar ===\n" . $lines->join("\n") . "\n=== E'lonlar tugadi ===";
    }

    private function detectRegionId(string $query): ?int
    {
        static $regions = null;
        $regions ??= Region::select('id', 'name')->get();

        foreach ($regions as $region) {
            $name = mb_strtolower($region->name);
            if (str_contains($query, $name)) {
                return $region->id;
            }
        }

        return null;
    }

    private function detectCategoryId(string $query): ?int
    {
        static $categories = null;
        $categories ??= Category::select('id', 'name')->get();

        foreach ($categories as $cat) {
            $catName = mb_strtolower($cat->name);
            if (str_contains($query, $catName)) {
                return $cat->id;
            }
        }

        // animal keyword map orqali aniqlash
        foreach ($this->animalMap as $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($query, mb_strtolower($kw))) {
                    // Kategoriya nomidan qidirish
                    foreach ($categories as $cat) {
                        foreach ($keywords as $kw2) {
                            if (str_contains(mb_strtolower($cat->name), mb_strtolower($kw2))) {
                                return $cat->id;
                            }
                        }
                    }
                }
            }
        }

        return null;
    }
}
