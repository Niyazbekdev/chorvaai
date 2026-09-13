<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MarketAnalysisTools
{
    public function __construct(private ProductRetrieverService $retriever) {}

    /** Gemini formatidagi function_declarations */
    public function definitions(): array
    {
        return [
            [
                'name'        => 'get_market_stats',
                'description' => "Marketplacessdagi e'lonlarning narx statistikasini olish. Kategoriya va viloyat bo'yicha filtrlash mumkin.",
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'category'    => ['type' => 'string', 'description' => "Chorva kategoriyasi nomi (masalan: Qoramol, Qo'y). Bo'sh qoldirsa barcha kategoriyalar."],
                        'region'      => ['type' => 'string', 'description' => "Viloyat nomi. Bo'sh qoldirsa barcha viloyatlar."],
                        'months_back' => ['type' => 'integer', 'description' => 'Necha oy oldindan boshlab hisoblash. Default: 1'],
                    ],
                ],
            ],
            [
                'name'        => 'get_price_trend',
                'description' => "Kategoriya bo'yicha oylik narx o'zgarishini ko'rish. Narxlar oshyabdimi yoki tushyabdimi.",
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'category'    => ['type' => 'string', 'description' => 'Chorva kategoriyasi nomi (masalan: Qoramol).'],
                        'months_back' => ['type' => 'integer', 'description' => "Necha oy orqaga qarab trend ko'rish. Default: 6"],
                    ],
                    'required' => ['category'],
                ],
            ],
            [
                'name'        => 'get_cheapest_regions',
                'description' => "Muayyan kategoriya uchun o'rtacha narq bo'yicha eng arzon viloyatlar ro'yxati.",
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'category' => ['type' => 'string', 'description' => 'Chorva kategoriyasi nomi.'],
                        'limit'    => ['type' => 'integer', 'description' => 'Nechta viloyat qaytarilsin. Default: 5'],
                    ],
                    'required' => ['category'],
                ],
            ],
            [
                'name'        => 'compare_categories',
                'description' => "Barcha kategoriyalar bo'yicha o'rtacha narx va e'lon soni taqqoslamasi.",
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'months_back' => ['type' => 'integer', 'description' => "Necha oy ichidagi ma'lumot. Default: 1"],
                    ],
                ],
            ],
            [
                'name'        => 'search_products',
                'description' => "Foydalanuvchi so'roviga mos real e'lonlarni saytdan topib beradi. Narx, viloyat, kategoriya bo'yicha qidirish mumkin.",
                'parameters'  => [
                    'type'       => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => "Qidiruv so'rovi. Misol: 'Toshkentda 3 mlngacha qoramol', 'Samarqanddagi qo'ylar'"],
                    ],
                    'required' => ['query'],
                ],
            ],
        ];
    }

    public function execute(string $name, array $input): array
    {
        return match ($name) {
            'get_market_stats'     => $this->getMarketStats($input),
            'get_price_trend'      => $this->getPriceTrend($input),
            'get_cheapest_regions' => $this->getCheapestRegions($input),
            'compare_categories'   => $this->compareCategories($input),
            'search_products'      => $this->searchProducts($input),
            default                => ['error' => "Noma'lum tool: $name"],
        };
    }

    private function searchProducts(array $input): array
    {
        $products = $this->retriever->search($input['query'] ?? '', 6);

        if ($products->isEmpty()) {
            return ['message' => "So'rovga mos e'lonlar topilmadi."];
        }

        return ['listings' => $this->retriever->formatForContext($products)];
    }

    private function getMarketStats(array $input): array
    {
        $months   = max(1, (int) ($input['months_back'] ?? 1));
        $category = $input['category'] ?? null;
        $region   = $input['region'] ?? null;

        $query = DB::table('products as p')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->join('regions as r', 'r.id', '=', 'p.region_id')
            ->join('statuses as s', 's.id', '=', 'p.status_id')
            ->where('s.name', 'Faol')
            ->where('p.created_at', '>=', now()->subMonths($months))
            ->selectRaw('
                ROUND(AVG(p.price)) as avg_price,
                MIN(p.price) as min_price,
                MAX(p.price) as max_price,
                COUNT(*) as listing_count
            ');

        if ($category) {
            $query->where('c.name', 'like', "%$category%");
        }
        if ($region) {
            $query->where('r.name', 'like', "%$region%");
        }

        $row = $query->first();

        return [
            'period'        => "So'nggi $months oy",
            'category'      => $category ?? 'Barcha kategoriyalar',
            'region'        => $region ?? 'Barcha viloyatlar',
            'avg_price'     => (int) ($row->avg_price ?? 0),
            'min_price'     => (int) ($row->min_price ?? 0),
            'max_price'     => (int) ($row->max_price ?? 0),
            'listing_count' => (int) ($row->listing_count ?? 0),
        ];
    }

    private function getPriceTrend(array $input): array
    {
        $category = $input['category'];
        $months   = max(1, min(24, (int) ($input['months_back'] ?? 6)));

        $rows = DB::table('products as p')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->join('statuses as s', 's.id', '=', 'p.status_id')
            ->where('s.name', 'Faol')
            ->where('p.created_at', '>=', now()->subMonths($months))
            ->where('c.name', 'like', "%$category%")
            ->selectRaw("DATE_FORMAT(p.created_at, '%Y-%m') as month, ROUND(AVG(p.price)) as avg_price, COUNT(*) as listing_count")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'month'         => $r->month,
                'avg_price'     => (int) $r->avg_price,
                'listing_count' => (int) $r->listing_count,
            ])
            ->all();

        return [
            'category' => $category,
            'period'   => "$months oy",
            'trend'    => $rows,
        ];
    }

    private function getCheapestRegions(array $input): array
    {
        $category = $input['category'];
        $limit    = min(10, max(1, (int) ($input['limit'] ?? 5)));

        $rows = DB::table('products as p')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->join('regions as r', 'r.id', '=', 'p.region_id')
            ->join('statuses as s', 's.id', '=', 'p.status_id')
            ->where('s.name', 'Faol')
            ->where('c.name', 'like', "%$category%")
            ->selectRaw('r.name as region, ROUND(AVG(p.price)) as avg_price, COUNT(*) as listing_count')
            ->groupBy('r.id', 'r.name')
            ->orderBy('avg_price')
            ->limit($limit)
            ->get()
            ->map(fn($r) => [
                'region'        => $r->region,
                'avg_price'     => (int) $r->avg_price,
                'listing_count' => (int) $r->listing_count,
            ])
            ->all();

        return ['category' => $category, 'regions' => $rows];
    }

    private function compareCategories(array $input): array
    {
        $months = max(1, (int) ($input['months_back'] ?? 1));

        $rows = DB::table('products as p')
            ->join('categories as c', 'c.id', '=', 'p.category_id')
            ->join('statuses as s', 's.id', '=', 'p.status_id')
            ->where('s.name', 'Faol')
            ->whereNull('c.parent_id')
            ->where('p.created_at', '>=', now()->subMonths($months))
            ->selectRaw('c.name as category, ROUND(AVG(p.price)) as avg_price, COUNT(*) as listing_count')
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('listing_count')
            ->get()
            ->map(fn($r) => [
                'category'      => $r->category,
                'avg_price'     => (int) $r->avg_price,
                'listing_count' => (int) $r->listing_count,
            ])
            ->all();

        return ['period' => "So'nggi $months oy", 'categories' => $rows];
    }
}
