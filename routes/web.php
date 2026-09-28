<?php

use App\Http\Controllers\Admin\AdminAreaController;
use App\Http\Controllers\Admin\AdminHistoryController;
use App\Http\Controllers\Admin\AdminRateController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::redirect('/', '/dashboard');

// Public / Operator Accessible Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/slots', [DashboardController::class, 'slotsMap'])->name('dashboard.slots');
Route::get('/api/dashboard/stats', [DashboardController::class, 'apiStats'])->name('api.dashboard.stats');

// Fitur Kendaraan Masuk & Cetak Tiket QR
Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.index');
Route::post('/checkin', [CheckInController::class, 'store'])->name('checkin.store');
Route::get('/checkin/ticket/{code}', [CheckInController::class, 'show'])->name('checkin.show');
Route::get('/checkin/ticket/{code}/print', [CheckInController::class, 'printTicket'])->name('checkin.print');

// Fitur Kendaraan Keluar & Scanner QR
Route::get('/checkout', [CheckOutController::class, 'index'])->name('checkout.index');
Route::post('/api/checkout/lookup', [CheckOutController::class, 'lookup'])->name('checkout.lookup');
Route::post('/checkout/process', [CheckOutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/receipt/{code}', [CheckOutController::class, 'receipt'])->name('checkout.receipt');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Admin Panel Routes (Protected by Auth & Admin Middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Kelola Area & Slot Parkir
    Route::get('/areas', [AdminAreaController::class, 'index'])->name('areas.index');
    Route::post('/areas', [AdminAreaController::class, 'store'])->name('areas.store');
    Route::put('/areas/{id}', [AdminAreaController::class, 'update'])->name('areas.update');
    Route::delete('/areas/{id}', [AdminAreaController::class, 'destroy'])->name('areas.destroy');

    // Kelola Tiket & Kendaraan Sedang Parkir
    Route::get('/tickets/active', [AdminTicketController::class, 'activeTickets'])->name('tickets.active');
    Route::post('/tickets/{id}/force-exit', [AdminTicketController::class, 'forceExit'])->name('tickets.force-exit');

    // Riwayat & Laporan Transaksi
    Route::get('/history', [AdminHistoryController::class, 'index'])->name('history.index');
    Route::get('/history/report', [AdminHistoryController::class, 'report'])->name('history.report');

    // Kelola Tarif Parkir
    Route::get('/rates', [AdminRateController::class, 'index'])->name('rates.index');
    Route::post('/rates', [AdminRateController::class, 'update'])->name('rates.update');

    // Kelola Pengguna (Admin & Operator)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});
