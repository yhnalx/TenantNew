<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Tenant\TenantController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\DashboardController;


// Default route
Route::get('/', function () {
    return redirect()->route('login');
});

// Login routes
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Register routes
Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Manager routes
Route::middleware(['auth', 'manager'])->group(function () {
    Route::get('/manager/dashboard', [DashboardController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/manager/reports', [DashboardController::class, 'reports'])->name('manager.reports');
    Route::get('/manager/settings', [DashboardController::class, 'settings'])->name('manager.settings');
    Route::get('/manager/tenants', [DashboardController::class, 'manageTenants'])->name('manager.tenants');
        
    Route::post('/manager/approve/{id}', [ManagerController::class, 'approve'])->name('manager.approve');
    Route::post('/manager/reject/{id}', [ManagerController::class, 'reject'])->name('manager.reject');
});

// Tenant routes
Route::middleware(['auth'])->group(function () {
    Route::get('/tenant/home', [TenantController::class, 'dashboard'])->name('tenant.home');
    Route::get('/tenant/payments', [TenantController::class, 'payments'])->name('tenant.payments');
    Route::get('/tenant/requests', [TenantController::class, 'requests'])->name('tenant.requests');
});

