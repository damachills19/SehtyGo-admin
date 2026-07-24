<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiderPayoutController;
use App\Http\Controllers\SupabaseWebhookController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/webhooks/supabase/new-registration', [SupabaseWebhookController::class, 'newRegistration'])
    ->name('webhooks.supabase.new-registration');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::patch('/approvals/{role}/{id}', [ApprovalController::class, 'updateStatus'])->name('approvals.update');

    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::patch('/accounts/{role}/{id}', [AccountController::class, 'toggleSuspend'])->name('accounts.toggle');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

    Route::get('/riders/payouts', [RiderPayoutController::class, 'index'])->name('riders.payouts');

    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
    Route::delete('/catalog/{id}', [CatalogController::class, 'destroy'])->name('catalog.destroy');

    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::patch('/support/{id}', [SupportController::class, 'updateStatus'])->name('support.update');
    Route::post('/support/{id}/reply', [SupportController::class, 'reply'])->name('support.reply');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
