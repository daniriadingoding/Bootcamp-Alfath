<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FoodMenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\NearbyController;
use App\Http\Controllers\AdminMerchantController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::resource('foodmenu', FoodMenuController::class)
     ->parameters(['foodmenu' => 'foodMenu']);
     
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
    Route::get('/explore-nearby', [NearbyController::class, 'index'])->name('nearby.index');
    Route::get('/api/nearby-merchants', [NearbyController::class, 'search'])->name('nearby.search');

    Route::get('/orders/{order}/payment', [PaymentController::class, 'createPayment'])->name('payment.create');
    Route::get('/orders/{order}/payment/success', [PaymentController::class, 'success'])->name('payment.success');
});

Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin/merchants', [AdminMerchantController::class, 'index'])->name('admin.merchants.index');
    Route::get('/admin/merchants/{merchant}/edit', [AdminMerchantController::class, 'edit'])->name('admin.merchants.edit');
    Route::put('/admin/merchants/{merchant}', [AdminMerchantController::class, 'update'])->name('admin.merchants.update');

    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
});

require __DIR__.'/auth.php';