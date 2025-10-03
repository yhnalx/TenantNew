<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\MaintenanceRequestController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\ReportsController;
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Controllers\Tenant\TenantApplicationController;
use App\Http\Controllers\Tenant\TenantRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Manager\UnitController;

// Default route
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/', [LandingController::class, 'index'])->name('landing');

// -----------------------------
// Auth routes
// -----------------------------
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', action: [RegisterController::class, 'index'])->name('register');
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
// Route::middleware(['auth', 'manager'])->group(function () {
//     Route::get('/manager/dashboard', [DashboardController::class, 'dashboard'])->name('manager.dashboard');
//     Route::get('/manager/settings', [DashboardController::class, 'settings'])->name('manager.settings');
//     Route::get('/manager/tenants', [DashboardController::class, 'manageTenants'])->name('manager.tenants');

//     // Reports
//     Route::get('/manager/reports', [ReportsController::class, 'index'])->name('manager.reports');
//     Route::get('/manager/reports/{report}', [ReportsController::class, 'show'])->name('manager.reports.show');
//     Route::get('manager/reports/{report}/export', [ReportsController::class, 'export'])
//     ->name('manager.reports.export');
//     Route::patch('/manager/payments/{payment}/status', [ReportsController::class, 'updatePaymentStatus'])
//     ->name('manager.payments.updateStatus');

//     // Requests
//     Route::patch('/manager/requests/{id}/status', [App\Http\Controllers\Manager\MaintenanceRequestController::class, 'updateStatus'])
//     ->name('manager.requests.updateStatus');
//     Route::get('/manager/requests/{id}', [App\Http\Controllers\Manager\MaintenanceRequestController::class, 'show'])
//         ->name('manager.requests.show');

//     // Tenant approval actions
//     Route::post('/manager/approve/{id}', [ManagerController::class, 'approve'])->name('manager.approve');
//     Route::post('/manager/reject/{id}', [ManagerController::class, 'reject'])->name('manager.reject');

//     // Tenant Unit Controller
//     // Units Management
//     // Manager Unit Routes
//     Route::get('/manager/units', [App\Http\Controllers\Manager\UnitController::class, 'index'])
//         ->name('manager.units.index');

//     Route::post('/manager/units', [App\Http\Controllers\Manager\UnitController::class, 'store'])
//         ->name('manager.units.store');

//     Route::get('/manager/units/{unit}/edit', [App\Http\Controllers\Manager\UnitController::class, 'edit'])
//         ->name('manager.units.edit');

//     Route::put('/manager/units/{unit}', [App\Http\Controllers\Manager\UnitController::class, 'update'])
//         ->name('manager.units.update');

//     Route::delete('/manager/units/{unit}', [App\Http\Controllers\Manager\UnitController::class, 'destroy'])
//         ->name('manager.units.destroy');

// });


Route::middleware(['auth', 'manager'])->group(function () {
    // Dashboard & Settings
    Route::get('/manager/dashboard', [DashboardController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/manager/settings', [DashboardController::class, 'settings'])->name('manager.settings');
    Route::get('/manager/tenants', [DashboardController::class, 'manageTenants'])->name('manager.tenants');

    // Reports
    Route::get('/manager/reports', [ReportsController::class, 'index'])->name('manager.reports');
    Route::get('/manager/reports/{report}', [ReportsController::class, 'show'])->name('manager.reports.show');
    Route::get('/manager/reports/{report}/export', [ReportsController::class, 'export'])->name('manager.reports.export');
    Route::patch('/manager/payments/{payment}/status', [ReportsController::class, 'updatePaymentStatus'])->name('manager.payments.updateStatus');

    Route::get('/manager/reports/{report}/export', [ReportsController::class, 'export'])->name('manager.reports.export');

    // Requests
    Route::patch('/manager/requests/{id}/status', [MaintenanceRequestController::class, 'updateStatus'])->name('manager.requests.updateStatus');
    Route::get('/manager/requests/{id}', [MaintenanceRequestController::class, 'show'])->name('manager.requests.show');

    // Tenant approval actions
    Route::get('/manager/tenants', [ManagerController::class, 'tenants'])->name('manager.tenants');
    Route::post('/manager/tenant/approve/{id}', [ManagerController::class, 'approve'])->name('manager.tenant.approve');
    Route::post('/manager/tenant/reject', [ManagerController::class, 'reject'])
        ->name('manager.tenant.reject');

    // Units Management
    Route::get('/manager/units', [UnitController::class, 'index'])->name('manager.units.index');
    Route::post('/manager/units', [UnitController::class, 'store'])->name('manager.units.store');
    Route::get('/manager/units/{unit}/edit', [UnitController::class, 'edit'])->name('manager.units.edit');
    Route::put('/manager/units/{unit}', [UnitController::class, 'update'])->name('manager.units.update');
    Route::delete('/manager/units/{unit}', [UnitController::class, 'destroy'])->name('manager.units.destroy');
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

    Route::get('/tenant/requests', [TenantRequestController::class, 'index'])
        ->name('tenant.requests');
    Route::post('/tenant/requests/store', [TenantRequestController::class, 'store'])
        ->name('tenant.requests.store');

});

