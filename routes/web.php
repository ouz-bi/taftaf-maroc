<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AddressController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');

// Produits
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{product:slug}', [ProductController::class, 'show'])->name('products.show');
});

// Panier
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/', [CartController::class, 'clear'])->name('cart.clear');
});

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Routes protégées (nécessitent authentification)
Route::middleware(['auth'])->group(function () {

    // Commandes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/{order}/track', [OrderController::class, 'track'])->name('orders.track');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    });

    // Adresses
    Route::resource('addresses', AddressController::class);

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Checkout invité (accessible sans connexion)
Route::prefix('guest')->group(function () {
    Route::get('/checkout', [App\Http\Controllers\GuestCheckoutController::class, 'index'])
        ->name('guest.checkout');
    Route::post('/checkout', [App\Http\Controllers\GuestCheckoutController::class, 'store'])
        ->name('guest.checkout.store');
    Route::get('/order/{order}/success', [App\Http\Controllers\GuestCheckoutController::class, 'success'])
        ->name('guest.order.success');
    Route::get('/track/{orderNumber}', [App\Http\Controllers\GuestCheckoutController::class, 'track'])
        ->name('guest.order.track');
});

Route::middleware('auth')->group(function () {
    // Rediriger selon le rôle après login
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('home');
    })->name('dashboard');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // Orders
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])
        ->name('admin.orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])
        ->name('admin.orders.show');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])
        ->name('admin.orders.updateStatus');

    // Products
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->names([
        'index' => 'admin.products.index',
        'create' => 'admin.products.create',
        'store' => 'admin.products.store',
        'edit' => 'admin.products.edit',
        'update' => 'admin.products.update',
        'destroy' => 'admin.products.destroy',
    ]);
    Route::patch('/products/{product}/toggle', [App\Http\Controllers\Admin\ProductController::class, 'toggleAvailability'])
        ->name('admin.products.toggle');

    // Categories
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class)->names([
        'index' => 'admin.categories.index',
        'store' => 'admin.categories.store',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

    // Customers
    Route::get('/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])
        ->name('admin.customers.index');
    Route::get('/customers/{user}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])
        ->name('admin.customers.show');

    // Riders
    Route::get('/riders', [App\Http\Controllers\Admin\RiderController::class, 'index'])
        ->name('admin.riders.index');
    Route::post('/riders', [App\Http\Controllers\Admin\RiderController::class, 'store'])
        ->name('admin.riders.store');

    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])
        ->name('admin.settings.index');
    Route::post('/settings/general', [App\Http\Controllers\Admin\SettingsController::class, 'updateGeneral'])
        ->name('admin.settings.general');
    Route::post('/settings/delivery', [App\Http\Controllers\Admin\SettingsController::class, 'updateDelivery'])
        ->name('admin.settings.delivery');
    Route::post('/settings/notifications', [App\Http\Controllers\Admin\SettingsController::class, 'updateNotifications'])
        ->name('admin.settings.notifications');
    Route::post('/settings/clear-cache', [App\Http\Controllers\Admin\SettingsController::class, 'clearCache'])
        ->name('admin.settings.clearCache');
    Route::post('/settings/reset', [App\Http\Controllers\Admin\SettingsController::class, 'reset'])
        ->name('admin.settings.reset');
});
