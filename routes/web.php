<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;

Route::get('/laporan', [LaporanController::class, 'laporan'])->name('laporan');

Route::view('/', 'welcome')->name('welcome');
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');
Route::view('/pesanan', 'order')->name('order');
Route::view('/inventory', 'inventorymaterial')->name('inventorymaterial');
Route::view('/keuangan', 'financial')->name('financial');

Route::resource('order', OrderController::class)->except(['show']);
Route::resource('inventorymaterial', InventoryController::class)->except(['show']);
Route::get('/finance', [DashboardController::class, 'finance'])->name('finance');
Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');