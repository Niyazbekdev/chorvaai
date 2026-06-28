<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

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
}
