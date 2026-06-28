<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Services\StatsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'totalUsers'     => User::count(),
            'totalProducts'  => Product::count(),
            'totalContacts'  => ContactInquiry::count(),
            'recentContacts' => ContactInquiry::latest()->take(10)->get(),
            'recentUsers'    => User::with('role')->latest()->take(8)->get(),
        ]);
    }

    public function users(): View
    {
        return view('admin.users', [
            'users' => User::with('role')->latest()->paginate(20),
            'roles' => Role::all(),
        ]);
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $request->validate(['role_id' => 'required|exists:roles,id']);
        $user->update(['role_id' => $request->role_id]);

        return back()->with('success', 'Rol yangilandi.');
    }

    public function products(): View
    {
        return view('admin.products', [
            'products' => Product::with(['user', 'category'])->latest()->paginate(20),
        ]);
    }

    public function contacts(): View
    {
        return view('admin.contacts', [
            'contacts' => ContactInquiry::latest()->paginate(20),
        ]);
    }

    public function deleteContact(ContactInquiry $contact): RedirectResponse
    {
        $contact->delete();

        return back()->with('success', "O'chirildi.");
    }

    public function stats(Request $request, StatsService $stats): View
    {
        $tab   = $request->get('tab', 'yearly');
        $years = range(now()->year, max(2024, now()->year - 4));

        // Yillik
        $selYear  = (int) $request->get('year', now()->year);
        $yearly   = $stats->yearly($selYear);

        // Oylik
        if ($request->filled('month_year')) {
            [$mYear, $mMonth] = array_map('intval', explode('-', $request->get('month_year')));
        } else {
            $mMonth = (int) $request->get('mmonth', now()->month);
            $mYear  = (int) $request->get('myear',  now()->year);
        }
        $monthly = $stats->monthly($mYear, $mMonth);

        // Belgilangan vaqt
        $dateFrom = $request->get('date_from', now()->subDays(29)->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());
        if ($dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }
        $custom = $stats->custom($dateFrom, $dateTo);

        return view('admin.stats', compact(
            'tab', 'years', 'selYear',
            'yearly',
            'mYear', 'mMonth', 'monthly',
            'dateFrom', 'dateTo', 'custom',
        ));
    }
}
