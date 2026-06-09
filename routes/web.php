<?php

use App\Http\Controllers\Admin\LegalDocumentSettingsController;
use App\Http\Controllers\Admin\BarionSettingsController;
use App\Http\Controllers\Admin\ContentImageController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PaymentMethodSettingsController;
use App\Http\Controllers\Admin\ShippingMethodSettingsController;
use App\Http\Controllers\Admin\SiteContentSettingsController;
use App\Http\Controllers\Admin\WebshopSettingsController;
use App\Models\LegalDocumentSetting;
use App\Models\PaymentMethodSetting;
use App\Models\ShippingMethodSetting;
use App\Models\SiteContentSetting;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BarionPaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PickupPointController;
use App\Models\PickupPoint;
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

    $legalDocuments = LegalDocumentSetting::current();
    $siteContent = SiteContentSetting::current();

    return view('welcome', compact('products', 'legalDocuments', 'siteContent'));
})->name('welcome');

Route::middleware('webshop.open')->group(function () {
    Route::get('/webshop', function () {
        $productsByCategory = Product::where('public', true)
            ->orderBy('category')
            ->orderBy('kedv')
            ->orderBy('title')
            ->get()
            ->groupBy('category');

        $enabledPaymentMethods = PaymentMethodSetting::current()->enabledMethods();
        $enabledShippingMethods = ShippingMethodSetting::current()->enabledMethods();
        $shippingFees = ShippingMethodSetting::current()->feesMap();
        $legalDocuments = LegalDocumentSetting::current();
        $carriersWithPickup = PickupPoint::carriersWithPoints();

        return view('webshop', compact('productsByCategory', 'enabledPaymentMethods', 'enabledShippingMethods', 'shippingFees', 'legalDocuments', 'carriersWithPickup'));
    })->name('webshop');

    Route::get('/pickup-points', [PickupPointController::class, 'index'])->name('pickup-points.index');
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
    Route::get('payment-methods', [PaymentMethodSettingsController::class, 'edit'])->name('payment-methods.edit');
    Route::put('payment-methods', [PaymentMethodSettingsController::class, 'update'])->name('payment-methods.update');
    Route::get('shipping-methods', [ShippingMethodSettingsController::class, 'edit'])->name('shipping-methods.edit');
    Route::put('shipping-methods', [ShippingMethodSettingsController::class, 'update'])->name('shipping-methods.update');
    Route::post('shipping-methods/sync-pickup-points', [ShippingMethodSettingsController::class, 'syncPickupPoints'])->name('shipping-methods.sync-pickup-points');
    Route::get('legal-documents', [LegalDocumentSettingsController::class, 'edit'])->name('legal-documents.edit');
    Route::put('legal-documents', [LegalDocumentSettingsController::class, 'update'])->name('legal-documents.update');
    Route::get('site-content/contact', [SiteContentSettingsController::class, 'editContact'])->name('site-content.contact.edit');
    Route::put('site-content/contact', [SiteContentSettingsController::class, 'updateContact'])->name('site-content.contact.update');
    Route::get('site-content/footer', [SiteContentSettingsController::class, 'editFooter'])->name('site-content.footer.edit');
    Route::put('site-content/footer', [SiteContentSettingsController::class, 'updateFooter'])->name('site-content.footer.update');
    Route::post('content-images', [ContentImageController::class, 'store'])->name('content-images.store');
});
