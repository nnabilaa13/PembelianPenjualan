<?php

use App\Http\Controllers\API\AuthController as ApiAuthController;
use App\Http\Controllers\WEB\AuthController;
use App\Http\Controllers\WEB\DashboardController;
use App\Http\Controllers\WEB\InventoryController;
use App\Http\Controllers\WEB\PurchaseController;
use App\Http\Controllers\WEB\SaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Tampilan)
|--------------------------------------------------------------------------
|
| Hanya digunakan untuk menyajikan halaman view / tampilan ke pengguna.
|
*/

// Redirect root to dashboard or login
Route::get('/', function () {
    if (session()->has('user')) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');
});

// Authentication view routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/logout', [ApiAuthController::class, 'logout'])->name('logout');

// Protected application view routes
Route::middleware(['role'])->group(function () {
    // Dashboard (All logged in users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Inventories view (SuperAdmin only)
    Route::middleware(['role:SuperAdmin'])->group(function () {
        Route::get('/inventories', [InventoryController::class, 'index'])->name('inventories.index');
    });

    // Purchases view & Print (SuperAdmin, Purchase, Manager)
    Route::middleware(['role:SuperAdmin,Purchase,Manager'])->group(function () {
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/{id}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    });

    // Sales view & Print (SuperAdmin, Sales, Manager)
    Route::middleware(['role:SuperAdmin,Sales,Manager'])->group(function () {
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/sales/{id}/print', [SaleController::class, 'print'])->name('sales.print');
    });
});
