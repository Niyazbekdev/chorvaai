<?php

use App\Http\Controllers\ContactEventController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/', fn () => view('welcome'))->name('home');

// Marketplace — public
Route::get('/marketplace', [ProductController::class, 'index'])->name('products.index');
Route::get('/marketplace/{product}', [ProductController::class, 'show'])->name('products.show');

// Contact event — guests can trigger too (viewer_id nullable)
Route::post('/marketplace/{product}/contact-event', [ContactEventController::class, 'store'])
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

    // Conversations & Messages
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
});

require __DIR__ . '/auth.php';
