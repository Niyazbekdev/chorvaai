<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Carbon;

class StatsService
{
    private array $monthNames = ['Yan','Fev','Mar','Apr','May','Iyn','Iyl','Avg','Sen','Okt','Noy','Dek'];

    public function yearly(int $year): array
    {
        $products = Product::selectRaw('MONTH(created_at) as m, COUNT(*) as cnt')
            ->whereYear('created_at', $year)
            ->groupBy('m')->get()->keyBy('m');

        $sales = Sale::selectRaw('MONTH(sold_at) as m, COUNT(*) as cnt, SUM(sold_price) as total')
            ->whereYear('sold_at', $year)
            ->groupBy('m')->get()->keyBy('m');

        $productData = $saleData = $saleSum = [];
        for ($m = 1; $m <= 12; $m++) {
            $productData[] = $products[$m]->cnt   ?? 0;
            $saleData[]    = $sales[$m]->cnt      ?? 0;
            $saleSum[]     = $sales[$m]->total    ?? 0;
        }

        return [
            'labels'      => $this->monthNames,
            'productData' => $productData,
            'saleData'    => $saleData,
            'saleSum'     => $saleSum,
            'totals' => [
                'products' => array_sum($productData),
                'sales'    => array_sum($saleData),
                'sum'      => array_sum($saleSum),
            ],
        ];
    }

    public function monthly(int $year, int $month): array
    {
        $products = Product::selectRaw('DAY(created_at) as d, COUNT(*) as cnt')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('d')->get()->keyBy('d');

        $sales = Sale::selectRaw('DAY(sold_at) as d, COUNT(*) as cnt, SUM(sold_price) as total')
            ->whereYear('sold_at', $year)
            ->whereMonth('sold_at', $month)
            ->groupBy('d')->get()->keyBy('d');

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $productData = $saleData = $saleSum = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $productData[] = $products[$d]->cnt   ?? 0;
            $saleData[]    = $sales[$d]->cnt      ?? 0;
            $saleSum[]     = $sales[$d]->total    ?? 0;
        }

        return [
            'labels'      => range(1, $daysInMonth),
            'productData' => $productData,
            'saleData'    => $saleData,
            'saleSum'     => $saleSum,
            'totals' => [
                'products' => array_sum($productData),
                'sales'    => array_sum($saleData),
                'sum'      => array_sum($saleSum),
            ],
        ];
    }

    public function custom(string $dateFrom, string $dateTo): array
    {
        $products = Product::selectRaw('DATE(created_at) as d, COUNT(*) as cnt')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->groupBy('d')->get()->keyBy('d');

        $sales = Sale::selectRaw('DATE(sold_at) as d, COUNT(*) as cnt, SUM(sold_price) as total')
            ->whereDate('sold_at', '>=', $dateFrom)
            ->whereDate('sold_at', '<=', $dateTo)
            ->groupBy('d')->get()->keyBy('d');

        $labels = $productData = $saleData = $saleSum = [];
        $cur = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);

        while ($cur <= $end) {
            $ds            = $cur->toDateString();
            $labels[]      = $cur->format('d.m');
            $productData[] = $products[$ds]->cnt   ?? 0;
            $saleData[]    = $sales[$ds]->cnt      ?? 0;
            $saleSum[]     = $sales[$ds]->total    ?? 0;
            $cur->addDay();
        }

        return [
            'labels'      => $labels,
            'productData' => $productData,
            'saleData'    => $saleData,
            'saleSum'     => $saleSum,
            'totals' => [
                'products' => array_sum($productData),
                'sales'    => array_sum($saleData),
                'sum'      => array_sum($saleSum),
            ],
        ];
    }

    public function monthNames(): array
    {
        return $this->monthNames;
    }
}
