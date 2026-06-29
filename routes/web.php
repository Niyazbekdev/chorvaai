<?php

use App\Http\Controllers\AiChatController;
use App\Http\Controllers\ContactEventController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

Route::get('/', fn () => view('welcome'))->name('home');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// AI Chat (guest va auth uchun)
Route::get('/ai-assistant', [AiChatController::class, 'page'])->name('ai-assistant.index');
Route::post('/ai-chat/send', [AiChatController::class, 'send'])->name('ai-chat.send');
Route::post('/ai-chat/send-with-file', [AiChatController::class, 'sendWithFile'])->name('ai-chat.send-file');
Route::post('/ai-chat/new', [AiChatController::class, 'newChat'])->name('ai-chat.new');
Route::get('/ai-chat/history', [AiChatController::class, 'history'])->name('ai-chat.history');

// Marketplace — public
Route::get('/marketplace', [ProductController::class, 'index'])->name('products.index');
Route::get('/marketplace/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/seller/{seller}', [\App\Http\Controllers\SellerController::class, 'show'])->name('seller.show');

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
    Route::get('/profile/my-products', [ProfileController::class, 'myProducts'])->name('profile.my-products');
    Route::get('/profile/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');

    // Phone change flow
    Route::post('/profile/phone/request', [ProfileController::class, 'requestPhoneChange'])->name('profile.phone.request');
    Route::post('/profile/phone/verify', [ProfileController::class, 'verifyPhoneChange'])->name('profile.phone.verify');
    Route::post('/profile/phone/resend', [ProfileController::class, 'resendPhoneOtp'])->middleware('throttle:3,1')->name('profile.phone.resend');
    Route::post('/profile/phone/cancel', [ProfileController::class, 'cancelPhoneChange'])->name('profile.phone.cancel');

    // Conversations & Messages
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])->name('messages.store');
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
