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

// Public routes
Route::view('/', 'welcome')->name('welcome');
Route::view('/login', 'login')->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/verify-email/{token}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::delete('/account', [RegisterController::class, 'deleteAccount'])->name('account.delete')->middleware('auth');

// Protected routes with role middleware
// Orders: viewing allowed for Owner, Admin, Produksi. Creating/updating allowed for Owner, Admin only.
Route::get('/pesanan', [OrderController::class, 'index'])->name('order')->middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin,Produksi');
Route::post('/pesanan', [OrderController::class, 'store'])->name('order.store')->middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin');
Route::post('/pesanan/production', [OrderController::class, 'storeProduction'])->name('order.production.store')->middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin');
Route::post('/pesanan/custom', [OrderController::class, 'storeCustom'])->name('order.custom.store')->middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin');
Route::put('/pesanan/{order}', [OrderController::class, 'update'])->name('order.update')->middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin');
Route::delete('/pesanan/{order}', [OrderController::class, 'destroy'])->name('order.destroy')->middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin');

// Inventory: Admin and Produksi can view; only Admin can modify
Route::get('/inventory', [MaterialController::class, 'index'])->name('inventorymaterial')->middleware(\App\Http\Middleware\CheckRole::class.':Admin,Produksi');
Route::post('/inventory', [MaterialController::class, 'store'])->name('inventorymaterial.store')->middleware(\App\Http\Middleware\CheckRole::class.':Admin');
Route::put('/inventory/{material}', [MaterialController::class, 'update'])->name('inventorymaterial.update')->middleware(\App\Http\Middleware\CheckRole::class.':Admin');
Route::delete('/inventory/{material}', [MaterialController::class, 'destroy'])->name('inventorymaterial.destroy')->middleware(\App\Http\Middleware\CheckRole::class.':Admin');

// Financial: only Keuangan role may access financial pages
Route::get('/keuangan', [FinancialController::class, 'index'])->name('financial')->middleware(\App\Http\Middleware\CheckRole::class.':Keuangan');
Route::get('/keuangan/export', [FinancialController::class, 'export'])->name('financial.export')->middleware(\App\Http\Middleware\CheckRole::class.':Keuangan');
Route::post('/keuangan/income', [FinancialController::class, 'storeIncome'])->name('financial.income.store')->middleware(\App\Http\Middleware\CheckRole::class.':Keuangan');
Route::post('/keuangan/expense', [FinancialController::class, 'storeExpense'])->name('financial.expense.store')->middleware(\App\Http\Middleware\CheckRole::class.':Keuangan');
Route::delete('/keuangan/{transaction}', [FinancialController::class, 'destroy'])->name('financial.destroy')->middleware(\App\Http\Middleware\CheckRole::class.':Keuangan');

// Laporan: Owner, Admin, Produksi can view general laporan. Exports are allowed for Owner and Keuangan
Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan')->middleware(\App\Http\Middleware\CheckRole::class.':Owner,Admin,Produksi');

// Temporary debug route: returns recent orders with relations as JSON
Route::get('/debug/orders', function () {
return \App\Models\Order::with(['customer', 'orderDetails.product', 'produksi', 'customDetail'])
->orderBy('OrderID', 'desc')
->take(50)
->get();
});
