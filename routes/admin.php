<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Produits
    Route::resource('products', AdminProductController::class);

    // Commandes
    Route::resource('orders', AdminOrderController::class);
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/assign-rider', [AdminOrderController::class, 'assignRider'])->name('orders.assign-rider');

    // Catégories
    Route::resource('categories', CategoryController::class);

    // Utilisateurs
    Route::resource('users', UserController::class);

    // Livreurs
    Route::resource('riders', RiderController::class);

    // Statistiques
    Route::get('/stats/sales', [DashboardController::class, 'salesStats'])->name('stats.sales');
    Route::get('/stats/products', [DashboardController::class, 'productStats'])->name('stats.products');
});
