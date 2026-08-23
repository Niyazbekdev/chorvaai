<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Region;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRetrieverService
{
    private const STOP_WORDS = [
        'bor', 'ham', 'yoki', 'uchun', 'qanday', 'qancha', 'saytda',
        'bilan', 'narxi', 'narxlari', 'sotib', 'olmoqchi', 'sotmoqchi',
        'menga', 'bizga', 'xohlaman', 'istaman', 'haqida', 'bormi',
        'и', 'в', 'на', 'с', 'за', 'по', 'для', 'что', 'как',
    ];

    // 'lar' deduplicated; 'лар' (Cyrillic plural) added
    private const SUFFIXES = [
        'lardan', 'larni', 'larda', 'larga', 'larmi', 'lar',
        'ning', 'dan', 'лар', 'ни', 'ов', 'ах', 'am', 'im',
    ];

    // 'toy' removed (false-positive: "toy sigir" = calf, not foal)
    // 'kid' removed from echki (English false-positive)
    private array $animalMap = [
        'sigir'   => ['sigir', 'qoramol', 'buqa', 'buz', 'корова', 'бык', 'буйвол', 'cow', 'bull'],
        'qoy'     => ["qo'y", 'qoy', 'qozy', 'toqli', "qo'zi", 'ovca', 'овца', 'баран', 'sheep', 'lamb', 'ram'],
        'echki'   => ['echki', 'uloq', 'коза', 'goat'],
        'ot'      => ['ot', 'biya', 'лошадь', 'конь', 'horse', 'mare', 'stallion'],
        'tuya'    => ['tuya', 'верблюд', 'camel'],
        'chochqa' => ["cho'chqa", 'chochqa', 'donuz', 'свинья', 'pig', 'boar'],
        'tovuq'   => ['tovuq', "xo'roz", "jo'ja", 'joʻja', 'курица', 'петух', 'chicken', 'hen', 'rooster'],
        'ordak'   => ["o'rdak", 'ordak', 'утка', 'duck'],
        'goz'     => ["g'oz", 'goz', 'гусь', 'goose'],
        'quyon'   => ['quyon', 'кролик', 'rabbit'],
    ];

    public function search(string $userQuery, int $limit = 8): Collection
    {
        $q = mb_strtolower(trim($userQuery));

        $priceRange = $this->detectPriceRange($q);
        $regionId   = $this->detectRegionId($q);
        $categoryId = $this->detectCategoryId($q);

        // Find IDs with progressive fallback (lightweight queries, no eager loading)
        $ids = $this->findIds($q, $priceRange, $regionId, $categoryId, $limit);

        if ($ids->isEmpty()) {
            return collect();
        }

        // Single eager-loaded fetch once we know which IDs to return
        return Product::whereIn('id', $ids)
            ->with(['category', 'region', 'city', 'status', 'color'])
            ->orderByDesc('views_count')
            ->get();
    }

    private function findIds(string $q, ?array $priceRange, ?int $regionId, ?int $categoryId, int $limit): Collection
    {
        // Fallback 1: barcha filterlar
        $ids = $this->queryIds($q, $priceRange, $regionId, $categoryId, $limit);

        // Fallback 2: matn filteri olmadan
        if ($ids->isEmpty() && ($regionId || $categoryId)) {
            $ids = $this->queryIds(null, $priceRange, $regionId, $categoryId, $limit);
        }

        // Fallback 3: region olmadan, faqat kategoriya
        if ($ids->isEmpty() && $categoryId) {
            $ids = $this->queryIds(null, $priceRange, null, $categoryId, $limit);
        }

        // Fallback 4: kategoriya olmadan, faqat region (#5 fix)
        if ($ids->isEmpty() && $regionId) {
            $ids = $this->queryIds(null, $priceRange, $regionId, null, $limit);
        }

        return $ids;
    }

    private function queryIds(
        ?string $textQuery,
        ?array $priceRange,
        ?int $regionId,
        ?int $categoryId,
        int $limit
    ): Collection {
        $builder = Product::query()
            ->select('id')
            ->where(function ($q) {
                // #7 fix: include products with no status (whereDoesntHave)
                $q->whereDoesntHave('status')
                  ->orWhereHas('status', fn ($s) => $s->where('name', 'not like', '%sotildi%'));
            });

        if ($priceRange) {
            [$min, $max] = $priceRange;
            if ($min > 0)                  $builder->where('price', '>=', $min);
            if ($max < 999_999_999_999)    $builder->where('price', '<=', $max);
        }

        if ($regionId) {
            $builder->where('region_id', $regionId);
        }

        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        if ($textQuery !== null) {
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

        return $builder->orderByDesc('views_count')->limit($limit)->pluck('id');
    }

    private function detectPriceRange(string $q): ?array
    {
        // "gacha" = up to (max only), "dan" = from (min only)
        $isUpTo   = str_contains($q, 'gacha') || str_contains($q, 'arzon') || str_contains($q, 'kam');
        $isFromAt = !$isUpTo && (bool) preg_match('/\bdan\b/u', $q);

        if (preg_match_all('/(\d+(?:[.,]\d+)?)\s*(?:million|mln|млн)/i', $q, $m)) {
            $amounts = array_map(fn ($n) => (int) ((float) str_replace(',', '.', $n) * 1_000_000), $m[1]);
            sort($amounts);
            if (count($amounts) >= 2) return [$amounts[0], $amounts[1]]; // range: "3 dan 5 mlngacha"
            $a = $amounts[0];
            if ($isUpTo)   return [0, $a];
            if ($isFromAt) return [$a, 999_999_999_999];
            return [(int)($a * 0.7), (int)($a * 1.3)];
        }

        if (preg_match_all('/(\d+(?:[.,]\d+)?)\s*(?:ming|тысяч|k\b)/i', $q, $m)) {
            $amounts = array_map(fn ($n) => (int) ((float) str_replace(',', '.', $n) * 1_000), $m[1]);
            sort($amounts);
            if (count($amounts) >= 2) return [$amounts[0], $amounts[1]];
            $a = $amounts[0];
            if ($isUpTo)   return [0, $a];
            if ($isFromAt) return [$a, 999_999_999_999];
            return [(int)($a * 0.7), (int)($a * 1.3)];
        }

        return null;
    }

    private function stemWords(string $query): array
    {
        $words = array_filter(
            preg_split('/[\s,\.!?]+/u', $query),
            fn ($w) => mb_strlen($w) > 2 && !in_array($w, self::STOP_WORDS)
        );

        $stems = [];
        foreach ($words as $word) {
            $stem = $word;
            foreach (self::SUFFIXES as $sfx) {
                if (mb_strlen($word) > mb_strlen($sfx) + 2 && mb_substr($word, -mb_strlen($sfx)) === $sfx) {
                    $stem = mb_substr($word, 0, -mb_strlen($sfx));
                    break;
                }
            }
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
            if ($p->color)  {
                $parts[] = "Rang: {$p->color->name}";
            }
            if ($p->gender) {
                $parts[] = "Jinsi: {$p->gender}";
            }
            if ($p->age !== null) {    // #4 fix: !== null, not falsy check
                $parts[] = "Yoshi: {$p->age} oy";
            }
            if ($p->weight !== null) { // #4 fix: !== null
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
        // #6 fix: plain array cached (Eloquent Collection doesn't deserialize reliably from DB cache)
        $regions = Cache::remember('retriever_regions', 1800, fn () =>
            Region::select('id', 'name')->get()
                  ->map(fn ($r) => ['id' => $r->id, 'name' => mb_strtolower($r->name)])
                  ->all()
        );

        foreach ($regions as $region) {
            if (str_contains($query, $region['name'])) {
                return $region['id'];
            }
        }

        return null;
    }

    private function detectCategoryId(string $query): ?int
    {
        // #6 fix: plain array cached
        $categories = Cache::remember('retriever_categories', 1800, fn () =>
            Category::select('id', 'name')->get()
                    ->map(fn ($c) => ['id' => $c->id, 'name' => mb_strtolower($c->name)])
                    ->all()
        );

        // Direct category name match (names are pre-lowercased in cache)
        foreach ($categories as $cat) {
            if (str_contains($query, $cat['name'])) {
                return $cat['id'];
            }
        }

        // #2 fix: word-boundary check for short keywords; simplified O(groups×categories) loop
        foreach ($this->animalMap as $keywords) {
            // Find which DB category corresponds to this animal group
            $catId = null;
            foreach ($categories as $cat) {
                foreach ($keywords as $kw) {
                    if (str_contains($cat['name'], mb_strtolower($kw))) {
                        $catId = $cat['id'];
                        break 2;
                    }
                }
            }

            if ($catId === null) {
                continue;
            }

            // Check query against every keyword in this group
            foreach ($keywords as $kw) {
                if ($this->keywordMatches($query, mb_strtolower($kw))) {
                    return $catId;
                }
            }
        }

        return null;
    }

    // #2 fix: short keywords (≤3 chars) must match as whole words to avoid substrings
    private function keywordMatches(string $query, string $kw): bool
    {
        if (mb_strlen($kw) <= 3) {
            return (bool) preg_match(
                '/(?:^|[\s,\.!\?])' . preg_quote($kw, '/') . '(?:$|[\s,\.!\?])/u',
                $query
            );
        }

        return str_contains($query, $kw);
    }
}
