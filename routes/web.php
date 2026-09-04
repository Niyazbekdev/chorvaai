<?php

use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ContactEventController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Payment\ClickController;
use App\Http\Controllers\Payment\PaymeController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/', function () {
    $categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();
    $recentProducts = \App\Models\Product::with(['category', 'region', 'city'])
        ->where('status_id', 1)
        ->latest()
        ->take(4)
        ->get();
    $stats = [
        'products' => \App\Models\Product::where('status_id', 1)->count(),
        'users'    => \App\Models\User::count(),
        'regions'  => \App\Models\Region::count(),
    ];
    return view('welcome', compact('categories', 'recentProducts', 'stats'));
})->name('home');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');

// Marketplace — public
Route::get('/marketplace', [ProductController::class, 'index'])->name('products.index');
Route::get('/marketplace/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/seller/{seller}', [\App\Http\Controllers\SellerController::class, 'show'])->name('seller.show');

// Contact event — guests can trigger too (viewer_id nullable)
Route::post('/marketplace/{product}/contact-event', [ContactEventController::class, 'store'])
    ->middleware('throttle:15,1')
    ->name('products.contact-event');

Route::middleware(['auth', 'phone.verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('products.index'))->name('dashboard');

    // Product CRUD
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Favorite toggle
    Route::post('/products/{product}/favorite', [FavoriteController::class, 'toggle'])
        ->name('products.favorite');

    // Mark as sold
    Route::post('/products/{product}/mark-sold', [SaleController::class, 'markAsSold'])
        ->name('products.mark-sold');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/my-products', [ProfileController::class, 'myProducts'])->name('profile.my-products');
    Route::get('/profile/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');

    // AI Agent — market analysis
    Route::get('/bozor-tahlili', [AiAgentController::class, 'index'])->name('ai.index');
    Route::post('/bozor-tahlili/chat', [AiAgentController::class, 'chat'])->middleware('throttle:20,1')->name('ai.chat');

    // Banners
    Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::get('/banners/my', [BannerController::class, 'myBanners'])->name('banners.my');
    Route::post('/banners/{banner}/reactivate', [BannerController::class, 'reactivate'])->name('banners.reactivate');
    Route::delete('/banners/{banner}', [BannerController::class, 'destroy'])->name('banners.destroy');

    // Phone change flow
    Route::post('/profile/phone/request', [ProfileController::class, 'requestPhoneChange'])->name('profile.phone.request');
    Route::post('/profile/phone/verify', [ProfileController::class, 'verifyPhoneChange'])->name('profile.phone.verify');
    Route::post('/profile/phone/resend', [ProfileController::class, 'resendPhoneOtp'])->middleware('throttle:3,1')->name('profile.phone.resend');
    Route::post('/profile/phone/cancel', [ProfileController::class, 'cancelPhoneChange'])->name('profile.phone.cancel');
});

// ── Payment webhooks (no auth — called by Payme/Click servers) ───────────────
Route::post('/webhook/payme', [PaymeController::class, 'handle'])
    ->name('webhook.payme')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/webhook/click/prepare', [ClickController::class, 'prepare'])
    ->name('webhook.click.prepare')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::post('/webhook/click/complete', [ClickController::class, 'complete'])
    ->name('webhook.click.complete')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ── Payment UI (auth required) ────────────────────────────────────────────────
Route::middleware(['auth', 'phone.verified'])->prefix('payment')->name('payment.')->group(function () {
    Route::get('/select', [PaymentController::class, 'select'])->name('select');
    Route::post('/initiate', [PaymentController::class, 'initiate'])->name('initiate');
    Route::get('/checkout/{payment}', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/failed', [PaymentController::class, 'failed'])->name('failed');
});

// Admin panel
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',          [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users',     [AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::get('/products',  [AdminController::class, 'products'])->name('products');
    Route::get('/contacts',  [AdminController::class, 'contacts'])->name('contacts');
    Route::delete('/contacts/{contact}', [AdminController::class, 'deleteContact'])->name('contacts.delete');
    Route::get('/stats',     [AdminController::class, 'stats'])->name('stats');
});

require __DIR__ . '/auth.php';
