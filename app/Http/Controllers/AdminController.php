<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalUsers'     => User::count(),
            'totalProducts'  => Product::count(),
            'totalContacts'  => ContactInquiry::count(),
            'recentContacts' => ContactInquiry::latest()->take(10)->get(),
            'recentUsers'    => User::with('role')->latest()->take(8)->get(),
        ]);
    }

    public function users()
    {
        $users = User::with('role')->latest()->paginate(20);
        $roles = Role::all();
        return view('admin.users', compact('users', 'roles'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user->update(['role_id' => $request->role_id]);
        return back()->with('success', 'Rol yangilandi.');
    }

    public function products()
    {
        $products = Product::with(['user', 'category'])->latest()->paginate(20);
        return view('admin.products', compact('products'));
    }

    public function contacts()
    {
        $contacts = ContactInquiry::latest()->paginate(20);
        return view('admin.contacts', compact('contacts'));
    }

    public function deleteContact(ContactInquiry $contact)
    {
        $contact->delete();
        return back()->with('success', "O'chirildi.");
    }

    public function stats(Request $request)
    {
        $tab        = $request->get('tab', 'yearly');
        $monthNames = ['Yan','Fev','Mar','Apr','May','Iyn','Iyl','Avg','Sen','Okt','Noy','Dek'];
        $years      = range(now()->year, max(2024, now()->year - 4));

        // ── Yillik ────────────────────────────────────────────
        $selYear = (int) $request->get('year', now()->year);

        $ypRaw = Product::selectRaw('MONTH(created_at) as m, COUNT(*) as cnt')
            ->whereYear('created_at', $selYear)
            ->groupBy('m')->get()->keyBy('m');
        $ysRaw = \App\Models\Sale::selectRaw('MONTH(sold_at) as m, COUNT(*) as cnt, SUM(sold_price) as total')
            ->whereYear('sold_at', $selYear)
            ->groupBy('m')->get()->keyBy('m');

        $yProductData = $ySaleData = $ySaleSum = [];
        for ($m = 1; $m <= 12; $m++) {
            $yProductData[] = $ypRaw[$m]->cnt   ?? 0;
            $ySaleData[]    = $ysRaw[$m]->cnt   ?? 0;
            $ySaleSum[]     = $ysRaw[$m]->total ?? 0;
        }
        $yTotalProducts = array_sum($yProductData);
        $yTotalSales    = array_sum($ySaleData);
        $yTotalSum      = array_sum($ySaleSum);

        // ── Oylik ─────────────────────────────────────────────
        if ($request->filled('month_year')) {
            [$mYear, $mMonth] = explode('-', $request->get('month_year'));
            $mYear  = (int) $mYear;
            $mMonth = (int) $mMonth;
        } else {
            $mMonth = (int) $request->get('mmonth', now()->month);
            $mYear  = (int) $request->get('myear',  now()->year);
        }

        $mpRaw = Product::selectRaw('DAY(created_at) as d, COUNT(*) as cnt')
            ->whereYear('created_at', $mYear)->whereMonth('created_at', $mMonth)
            ->groupBy('d')->get()->keyBy('d');
        $msRaw = \App\Models\Sale::selectRaw('DAY(sold_at) as d, COUNT(*) as cnt, SUM(sold_price) as total')
            ->whereYear('sold_at', $mYear)->whereMonth('sold_at', $mMonth)
            ->groupBy('d')->get()->keyBy('d');

        $daysInMonth   = cal_days_in_month(CAL_GREGORIAN, $mMonth, $mYear);
        $mLabels = range(1, $daysInMonth);
        $mProductData = $mSaleData = $mSaleSum = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $mProductData[] = $mpRaw[$d]->cnt   ?? 0;
            $mSaleData[]    = $msRaw[$d]->cnt   ?? 0;
            $mSaleSum[]     = $msRaw[$d]->total ?? 0;
        }
        $mTotalProducts = array_sum($mProductData);
        $mTotalSales    = array_sum($mSaleData);
        $mTotalSum      = array_sum($mSaleSum);

        // ── Belgilangan vaqt ──────────────────────────────────
        $dateFrom = $request->get('date_from', now()->subDays(29)->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());
        if ($dateFrom > $dateTo) [$dateFrom, $dateTo] = [$dateTo, $dateFrom];

        $cpRaw = Product::selectRaw('DATE(created_at) as d, COUNT(*) as cnt')
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->groupBy('d')->get()->keyBy('d');
        $csRaw = \App\Models\Sale::selectRaw('DATE(sold_at) as d, COUNT(*) as cnt, SUM(sold_price) as total')
            ->whereDate('sold_at', '>=', $dateFrom)
            ->whereDate('sold_at', '<=', $dateTo)
            ->groupBy('d')->get()->keyBy('d');

        $cLabels = $cProductData = $cSaleData = $cSaleSum = [];
        $cur = Carbon::parse($dateFrom);
        $end = Carbon::parse($dateTo);
        while ($cur <= $end) {
            $ds           = $cur->toDateString();
            $cLabels[]    = $cur->format('d.m');
            $cProductData[] = $cpRaw[$ds]->cnt   ?? 0;
            $cSaleData[]    = $csRaw[$ds]->cnt   ?? 0;
            $cSaleSum[]     = $csRaw[$ds]->total ?? 0;
            $cur->addDay();
        }
        $cTotalProducts = array_sum($cProductData);
        $cTotalSales    = array_sum($cSaleData);
        $cTotalSum      = array_sum($cSaleSum);

        return view('admin.stats', compact(
            'tab', 'monthNames', 'years',
            'selYear',
            'yProductData', 'ySaleData', 'ySaleSum', 'yTotalProducts', 'yTotalSales', 'yTotalSum',
            'mMonth', 'mYear', 'mLabels',
            'mProductData', 'mSaleData', 'mSaleSum', 'mTotalProducts', 'mTotalSales', 'mTotalSum',
            'dateFrom', 'dateTo', 'cLabels',
            'cProductData', 'cSaleData', 'cSaleSum', 'cTotalProducts', 'cTotalSales', 'cTotalSum',
        ));
    }
}
