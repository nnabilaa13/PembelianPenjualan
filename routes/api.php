<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\CacheController;
use App\Http\Controllers\API\CacheLockController;
use App\Http\Controllers\API\FailedJobController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\JobBatchController;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\PurchaseController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

// Authentication API
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

// Protected API routes using session role middleware
Route::middleware(['role'])->group(function () {
    // Select2 inventory options (Accessible by SuperAdmin, Purchase, Sales)
    Route::get('/inventories/select2', [InventoryController::class, 'select2'])->name('inventories.select2');

    // Master Inventories API (SuperAdmin only)
    Route::middleware(['role:SuperAdmin'])->group(function () {
        Route::get('/inventories/data', [InventoryController::class, 'data'])->name('inventories.data');
        Route::match(['get', 'post'], '/inventories_datatables', [InventoryController::class, 'datatables'])->name('datatable.inventories');
        Route::get('/inventories/{id?}', [InventoryController::class, 'get'])->name('get.inventories');
        Route::post('/inventories', [InventoryController::class, 'post'])->name('inventories.store');
        Route::put('/inventories/{id}', [InventoryController::class, 'put'])->name('inventories.update');
        Route::patch('/inventories/{id}', [InventoryController::class, 'patch'])->name('patch.inventories');
        Route::delete('/inventories/{id}', [InventoryController::class, 'delete'])->name('inventories.destroy');
    });

    // Purchases API (SuperAdmin, Purchase, Manager for read; SuperAdmin, Purchase for write)
    Route::middleware(['role:SuperAdmin,Purchase,Manager'])->group(function () {
        Route::get('/purchases/data', [PurchaseController::class, 'data'])->name('purchases.data');
        Route::match(['get', 'post'], '/purchases_datatables', [PurchaseController::class, 'datatables'])->name('datatable.purchases');
        Route::get('/purchases/{id?}', [PurchaseController::class, 'get'])->name('get.purchases');
    });

    Route::middleware(['role:SuperAdmin,Purchase'])->group(function () {
        Route::post('/purchases', [PurchaseController::class, 'post'])->name('purchases.store');
        Route::put('/purchases/{id}', [PurchaseController::class, 'put'])->name('purchases.update');
    });

    Route::middleware(['role:SuperAdmin,Purchase'])->group(function () {
        Route::delete('/purchases/{id}', [PurchaseController::class, 'delete'])->name('purchases.destroy');
    });

    // Sales API (SuperAdmin, Sales, Manager for read; SuperAdmin, Sales for write)
    Route::middleware(['role:SuperAdmin,Sales,Manager'])->group(function () {
        Route::get('/sales/data', [SaleController::class, 'data'])->name('sales.data');
        Route::match(['get', 'post'], '/sales_datatables', [SaleController::class, 'datatables'])->name('datatable.sales');
        Route::get('/sales/{id?}', [SaleController::class, 'get'])->name('get.sales');
    });

    Route::middleware(['role:SuperAdmin,Sales'])->group(function () {
        Route::post('/sales', [SaleController::class, 'post'])->name('sales.store');
        Route::put('/sales/{id}', [SaleController::class, 'put'])->name('sales.update');
    });

    Route::middleware(['role:SuperAdmin,Sales'])->group(function () {
        Route::delete('/sales/{id}', [SaleController::class, 'delete'])->name('sales.destroy');
    });

    // ---- Laracademy Generator Routes ----
    Route::controller(UserController::class)->group(function () {
        Route::get('users/{id?}', 'get')->name('get.users');
        Route::post('users', 'post')->name('post.users');
        Route::patch('users/{id}', 'patch')->name('patch.users');
        Route::put('users/{id}', 'put')->name('put.users');
        Route::delete('users/{id}', 'delete')->name('delete.users');
        Route::post('users_datatables', 'datatables')->name('datatable.users');
        Route::patch('users/{id}/approve', 'approve')->name('approve.users');
    });

    Route::controller(CacheController::class)->group(function () {
        Route::get('cache/{id?}', 'get')->name('get.cache');
        Route::post('cache', 'post')->name('post.cache');
        Route::patch('cache/{id}', 'patch')->name('patch.cache');
        Route::put('cache/{id}', 'put')->name('put.cache');
        Route::delete('cache/{id}', 'delete')->name('delete.cache');
        Route::post('cache_datatables', 'datatables')->name('datatable.cache');
        Route::patch('cache/{id}/approve', 'approve')->name('approve.cache');
    });

    Route::controller(CacheLockController::class)->group(function () {
        Route::get('cache_locks/{id?}', 'get')->name('get.cache_locks');
        Route::post('cache_locks', 'post')->name('post.cache_locks');
        Route::patch('cache_locks/{id}', 'patch')->name('patch.cache_locks');
        Route::put('cache_locks/{id}', 'put')->name('put.cache_locks');
        Route::delete('cache_locks/{id}', 'delete')->name('delete.cache_locks');
        Route::post('cache_locks_datatables', 'datatables')->name('datatable.cache_locks');
        Route::patch('cache_locks/{id}/approve', 'approve')->name('approve.cache_locks');
    });

    Route::controller(JobController::class)->group(function () {
        Route::get('jobs/{id?}', 'get')->name('get.jobs');
        Route::post('jobs', 'post')->name('post.jobs');
        Route::patch('jobs/{id}', 'patch')->name('patch.jobs');
        Route::put('jobs/{id}', 'put')->name('put.jobs');
        Route::delete('jobs/{id}', 'delete')->name('delete.jobs');
        Route::post('jobs_datatables', 'datatables')->name('datatable.jobs');
        Route::patch('jobs/{id}/approve', 'approve')->name('approve.jobs');
    });

    Route::controller(JobBatchController::class)->group(function () {
        Route::get('job_batches/{id?}', 'get')->name('get.job_batches');
        Route::post('job_batches', 'post')->name('post.job_batches');
        Route::patch('job_batches/{id}', 'patch')->name('patch.job_batches');
        Route::put('job_batches/{id}', 'put')->name('put.job_batches');
        Route::delete('job_batches/{id}', 'delete')->name('delete.job_batches');
        Route::post('job_batches_datatables', 'datatables')->name('datatable.job_batches');
        Route::patch('job_batches/{id}/approve', 'approve')->name('approve.job_batches');
    });

    Route::controller(FailedJobController::class)->group(function () {
        Route::get('failed_jobs/{id?}', 'get')->name('get.failed_jobs');
        Route::post('failed_jobs', 'post')->name('post.failed_jobs');
        Route::patch('failed_jobs/{id}', 'patch')->name('patch.failed_jobs');
        Route::put('failed_jobs/{id}', 'put')->name('put.failed_jobs');
        Route::delete('failed_jobs/{id}', 'delete')->name('delete.failed_jobs');
        Route::post('failed_jobs_datatables', 'datatables')->name('datatable.failed_jobs');
        Route::patch('failed_jobs/{id}/approve', 'approve')->name('approve.failed_jobs');
    });
});
