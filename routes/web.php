<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\MaterialPurchaseController;

// Public routes
Route::view('/', 'welcome')->name('welcome');
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Email verification
Route::get('/verify-email/{token}', [VerificationController::class, 'verify'])->name('verification.verify');

// Protected routes requiring authentication
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::delete('/account', [RegisterController::class, 'deleteAccount'])->name('account.delete');

    // Orders: Owner, Admin, Produksi
    Route::middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin,Produksi')->group(function () {
        Route::get('/pesanan', [OrderController::class, 'index'])->name('order');
        
        // Only Owner and Admin can create/update/delete
        Route::middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin')->group(function () {
            Route::post('/pesanan', [OrderController::class, 'store'])->name('order.store');
            Route::post('/pesanan/production', [OrderController::class, 'storeProduction'])->name('order.production.store');
            Route::post('/pesanan/custom', [OrderController::class, 'storeCustom'])->name('order.custom.store');
            Route::put('/pesanan/{order}', [OrderController::class, 'update'])->name('order.update');
            Route::delete('/pesanan/{order}', [OrderController::class, 'destroy'])->name('order.destroy');
        });
    });

    // Inventory: Admin and Produksi
    Route::middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin,Produksi')->group(function () {
        Route::get('/inventory', [MaterialController::class, 'index'])->name('inventorymaterial');
        
        // Only Owner and Produksi can modify
        Route::middleware(\App\Http\Middleware\CheckRole::class.':Owner,Produksi')->group(function () {
            Route::post('/inventory', [MaterialController::class, 'store'])->name('inventorymaterial.store');
            Route::put('/inventory/{material}', [MaterialController::class, 'update'])->name('inventorymaterial.update');
            Route::delete('/inventory/{material}', [MaterialController::class, 'destroy'])->name('inventorymaterial.destroy');
        });
    });

    // Financial: Keuangan, Owner, Admin
    Route::middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin,Keuangan')->group(function () {
        Route::get('/keuangan', [FinancialController::class, 'index'])->name('financial');
        Route::get('/keuangan/export', [FinancialController::class, 'export'])->name('financial.export');
        Route::post('/keuangan/income', [FinancialController::class, 'storeIncome'])->name('financial.income.store');
        Route::post('/keuangan/expense', [FinancialController::class, 'storeExpense'])->name('financial.expense.store');
        Route::delete('/keuangan/{transaction}', [FinancialController::class, 'destroy'])->name('financial.destroy');
        
        // Material purchases
        Route::get('/keuangan/material-purchases', [MaterialPurchaseController::class, 'index'])->name('materialpurchase.index');
        Route::post('/keuangan/material-purchases', [MaterialPurchaseController::class, 'store'])->name('materialpurchase.store');
    });

    // Reports: Owner, Admin, Produksi, Keuangan (semua role bisa akses laporan)
    Route::middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin,Produksi,Keuangan')->group(function () {
        Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan');
        
        // Export routes for reports
        Route::get('/laporan/export/pdf/{type}', [LaporanController::class, 'exportPDF'])->name('laporan.export.pdf');
        Route::get('/laporan/export/excel/{type}', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    });
});

// Debug route (hapus di production)
Route::get('/debug/orders', function () {
    return \App\Models\Order::with(['customer', 'orderDetails.product', 'produksi', 'customDetail'])
        ->orderBy('OrderID', 'desc')
        ->take(50)
        ->get();
});

// Debug route untuk cek user saat ini
Route::get('/debug/user', function () {
    return [
        'authenticated' => auth()->check(),
        'user' => auth()->user(),
    ];
})->middleware('auth');