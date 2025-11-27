<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LoanController;

Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/check-login', [AuthController::class, 'checkLogin'])->name('check.login');
Route::get('/forget-password', [AuthController::class, 'forgetPassword'])->name('forget.password');
Route::post('/submit-forget-password', [AuthController::class, 'submitForgetPassword'])->name('check.forget.password');
Route::get('/reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');
Route::post('/submit-reset-password', [AuthController::class, 'submitResetPassword'])->name('submit.reset.password');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/general-settings', [DashboardController::class, 'general_settings'])->name('admin.general.settings');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('admin.profile');
    Route::get('/change-password', [DashboardController::class, 'change_password'])->name('admin.change.password');
    Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::resource('customers', CustomerController::class);
    Route::get('/load-customers', [CustomerController::class, 'load'])->name('admin.customers.load');
    Route::get('/customer-loans', [CustomerController::class, 'customer_loans'])->name('admin.customer.loan.load');
    Route::get('/customer-export', [CustomerController::class, 'export'])->name('admin.customer.export');
    Route::get('/customer-details-export/{customer_id}', [CustomerController::class, 'customer_export'])->name('admin.customer.details.export');

    Route::resource('loans', LoanController::class);
    Route::get('/load-loans', [LoanController::class, 'load'])->name('admin.loans.load');
    Route::post('/import-loans', [LoanController::class, 'import'])->name('admin.loan.import');
});
