<?php

use App\Http\Controllers\Admin\BarionSettingsController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\WebshopSettingsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BarionPaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $products = Product::where('public', true)
        ->where('tomain', true)
        ->orderBy('kedv')
        ->orderBy('title')
        ->get();

    return view('welcome', compact('products'));
})->name('welcome');

Route::middleware('webshop.open')->group(function () {
    Route::get('/webshop', function () {
        $productsByCategory = Product::where('public', true)
            ->orderBy('category')
            ->orderBy('kedv')
            ->orderBy('title')
            ->get()
            ->groupBy('category');

        return view('webshop', compact('productsByCategory'));
    })->name('webshop');

    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});

Route::get('/payment/barion/return', [BarionPaymentController::class, 'return'])->name('payment.barion.return');
Route::post('/payment/barion/callback', [BarionPaymentController::class, 'callback'])->name('payment.barion.callback');

// Auth – vendég felhasználóknak
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Bejelentkezett felhasználó kezdőlapja
Route::get('/home', HomeController::class)->name('home')->middleware('auth');

// Admin – csak user id 1, 2, 3
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::get('orders/{order}/transactions', [AdminOrderController::class, 'transactions'])->name('orders.transactions');
    Route::resource('orders', AdminOrderController::class)->except(['create', 'store']);
    Route::get('barion', [BarionSettingsController::class, 'edit'])->name('barion.edit');
    Route::put('barion', [BarionSettingsController::class, 'update'])->name('barion.update');
    Route::get('webshop', [WebshopSettingsController::class, 'edit'])->name('webshop.edit');
    Route::put('webshop', [WebshopSettingsController::class, 'update'])->name('webshop.update');
});
