<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\FinancialController;

// Public routes
Route::view('/', 'welcome')->name('welcome');
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');

// Protected routes (add auth middleware later)
Route::get('/pesanan', [OrderController::class, 'index'])->name('order');
Route::post('/pesanan', [OrderController::class, 'store'])->name('order.store');
Route::post('/pesanan/production', [OrderController::class, 'storeProduction'])->name('order.production.store');
Route::post('/pesanan/custom', [OrderController::class, 'storeCustom'])->name('order.custom.store');
Route::put('/pesanan/{order}', [OrderController::class, 'update'])->name('order.update');
Route::delete('/pesanan/{order}', [OrderController::class, 'destroy'])->name('order.destroy');

Route::get('/inventory', [MaterialController::class, 'index'])->name('inventorymaterial');
Route::post('/inventory', [MaterialController::class, 'store'])->name('inventorymaterial.store');
Route::put('/inventory/{material}', [MaterialController::class, 'update'])->name('inventorymaterial.update');
Route::delete('/inventory/{material}', [MaterialController::class, 'destroy'])->name('inventorymaterial.destroy');

Route::get('/keuangan', [FinancialController::class, 'index'])->name('financial');
Route::get('/keuangan/export', [FinancialController::class, 'export'])->name('financial.export');
Route::post('/keuangan/income', [FinancialController::class, 'storeIncome'])->name('financial.income.store');
Route::post('/keuangan/expense', [FinancialController::class, 'storeExpense'])->name('financial.expense.store');
Route::delete('/keuangan/{transaction}', [FinancialController::class, 'destroy'])->name('financial.destroy');

Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan');
Route::get('/laporan/export-pdf/{type}', [LaporanController::class, 'exportPDF'])->name('laporan.export.pdf');
Route::get('/laporan/export-excel/{type}', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');