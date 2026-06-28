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
        $tab = $request->get('tab', 'weekly');

        // ── Kunlik ────────────────────────────────────────────
        $selectedDate = $request->get('date', today()->toDateString());
        $dailyRaw = Product::selectRaw('HOUR(created_at) as h, COUNT(*) as cnt')
            ->whereDate('created_at', $selectedDate)
            ->groupBy('h')->orderBy('h')->get()->keyBy('h');
        $dailyLabels = [];
        $dailyData   = [];
        for ($h = 0; $h < 24; $h++) {
            $dailyLabels[] = sprintf('%02d:00', $h);
            $dailyData[]   = $dailyRaw[$h]->cnt ?? 0;
        }
        $dailyTotal = array_sum($dailyData);

        // ── Haftalik ──────────────────────────────────────────
        $weeklyRaw = Product::selectRaw('DATE(created_at) as d, COUNT(*) as cnt')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('d')->orderBy('d')->get()->keyBy('d');
        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 6; $i >= 0; $i--) {
            $date           = now()->subDays($i)->toDateString();
            $weeklyLabels[] = now()->subDays($i)->format('d.m');
            $weeklyData[]   = $weeklyRaw[$date]->cnt ?? 0;
        }
        $weeklyTotal = array_sum($weeklyData);

        // ── Oylik ─────────────────────────────────────────────
        // input type=month yuboradi "2026-06" formatida
        if ($request->filled('month_year')) {
            [$selYear, $selMonth] = explode('-', $request->get('month_year'));
            $selYear  = (int) $selYear;
            $selMonth = (int) $selMonth;
        } else {
            $selMonth = (int) $request->get('month', now()->month);
            $selYear  = (int) $request->get('year',  now()->year);
        }
        $monthlyRaw = Product::selectRaw('DAY(created_at) as d, COUNT(*) as cnt')
            ->whereYear('created_at', $selYear)->whereMonth('created_at', $selMonth)
            ->groupBy('d')->orderBy('d')->get()->keyBy('d');
        $daysInMonth   = cal_days_in_month(CAL_GREGORIAN, $selMonth, $selYear);
        $monthlyLabels = [];
        $monthlyData   = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $monthlyLabels[] = $d;
            $monthlyData[]   = $monthlyRaw[$d]->cnt ?? 0;
        }
        $monthlyTotal = array_sum($monthlyData);

        // ── Yillik ────────────────────────────────────────────
        $selYearlyYear = (int) $request->get('yearly_year', now()->year);
        $yearlyRaw = Product::selectRaw('MONTH(created_at) as m, COUNT(*) as cnt')
            ->whereYear('created_at', $selYearlyYear)
            ->groupBy('m')->orderBy('m')->get()->keyBy('m');
        $monthNames  = ['Yan','Fev','Mar','Apr','May','Iyn','Iyl','Avg','Sen','Okt','Noy','Dek'];
        $yearlyData  = [];
        for ($m = 1; $m <= 12; $m++) {
            $yearlyData[] = $yearlyRaw[$m]->cnt ?? 0;
        }
        $yearlyTotal = array_sum($yearlyData);

        $years = range(now()->year, max(2024, now()->year - 4));

        return view('admin.stats', compact(
            'tab',
            'dailyLabels',  'dailyData',  'dailyTotal',  'selectedDate',
            'weeklyLabels', 'weeklyData', 'weeklyTotal',
            'monthlyLabels','monthlyData','monthlyTotal', 'selMonth', 'selYear',
            'monthNames',   'yearlyData', 'yearlyTotal',  'selYearlyYear', 'years'
        ));
    }
}
