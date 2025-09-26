<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Manager\DashboardController;

use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\ReportsController;
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Controllers\Tenant\TenantApplicationController;
use Illuminate\Support\Facades\Route;

// Default route
Route::get('/', function () {
    return redirect()->route('login');
});

// -----------------------------
// Auth routes
// -----------------------------
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// -----------------------------
// Manager routes (only managers)
// -----------------------------
// Route::middleware(['auth', 'manager'])->group(function () {
//     Route::get('/manager/dashboard', [DashboardController::class, 'dashboard'])->name('manager.dashboard');
//     Route::get('/manager/reports', [DashboardController::class, 'reports'])->name('manager.reports');
//     Route::get('/manager/settings', [DashboardController::class, 'settings'])->name('manager.settings');
//     Route::get('/manager/tenants', [DashboardController::class, 'manageTenants'])->name('manager.tenants');
    
//     Route::post('/manager/approve/{id}', [ManagerController::class, 'approve'])->name('manager.approve');
//     Route::post('/manager/reject/{id}', [ManagerController::class, 'reject'])->name('manager.reject');
// });
Route::middleware(['auth', 'manager'])->group(function () {
    Route::get('/manager/dashboard', [DashboardController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/manager/settings', [DashboardController::class, 'settings'])->name('manager.settings');
    Route::get('/manager/tenants', [DashboardController::class, 'manageTenants'])->name('manager.tenants');

    // Reports
    Route::get('/manager/reports', [ReportsController::class, 'index'])->name('manager.reports');
    Route::get('/manager/reports/{report}', [ReportsController::class, 'show'])->name('manager.reports.show');

    // Tenant approval actions
    Route::post('/manager/approve/{id}', [ManagerController::class, 'approve'])->name('manager.approve');
    Route::post('/manager/reject/{id}', [ManagerController::class, 'reject'])->name('manager.reject');
});

 
// Tenant application form (modal on dashboard)
Route::middleware(['auth'])->group(function () {
    Route::get('/tenant/application', [TenantApplicationController::class, 'show'])
        ->name('tenant.application');
    Route::post('/tenant/application/submit', [TenantApplicationController::class, 'submit'])
        ->name('tenant.application.submit');
});

// Tenant dashboard/routes
Route::middleware(['auth'])->group(function () {
    Route::get('/tenant/home', [TenantController::class, 'dashboard'])->name('tenant.home');
    Route::get('/tenant/payments', [TenantController::class, 'payments'])->name('tenant.payments');
    Route::get('/tenant/requests', [TenantController::class, 'requests'])->name('tenant.requests');

    // Tenant application submission (modal on dashboard)
    Route::post('/tenant/application/submit', [TenantApplicationController::class, 'submit'])
        ->name('tenant.application.submit'); 

    Route::get('/tenant/payments', [TenantPaymentController::class, 'index'])
        ->name('tenant.payments');

    // Tenant make payment
    Route::post('/tenant/payments/store', [TenantPaymentController::class, 'store'])
        ->name('tenant.payments.store');

});

