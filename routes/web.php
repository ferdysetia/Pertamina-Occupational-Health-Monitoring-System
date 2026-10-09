<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DailyCheckUpController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\HealthAlertController;
use App\Http\Controllers\McuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserEmployeeProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Dashboard (All Authenticated Roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employee Bio Data Profile Update (All Authenticated Roles: Admin, HSE, Doctor, Manager, Employee)
    Route::put('/user/employee-profile', [UserEmployeeProfileController::class, 'update'])->name('user-employee-profile.update');

    // Employees
    Route::get('/employees', [EmployeeController::class, 'index'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('employees.index');

    Route::get('/employees/{id}', [EmployeeController::class, 'show'])
        ->name('employees.show');

    // MCU Management (Periodic)
    Route::get('/mcu', [McuController::class, 'index'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('mcu.index');

    Route::get('/mcu/create', [McuController::class, 'create'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('mcu.create');

    Route::post('/mcu', [McuController::class, 'store'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('mcu.store');

    Route::get('/mcu/{id}', [McuController::class, 'show'])
        ->name('mcu.show');

    Route::get('/mcu/{id}/edit', [McuController::class, 'edit'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('mcu.edit');

    Route::put('/mcu/{id}', [McuController::class, 'update'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('mcu.update');

    // Daily Check Up (DCU) Management (Daily Fit to Work)
    Route::get('/daily-check-ups', [DailyCheckUpController::class, 'index'])->name('daily-check-ups.index');

    Route::get('/daily-check-ups/create', [DailyCheckUpController::class, 'create'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('daily-check-ups.create');

    Route::post('/daily-check-ups', [DailyCheckUpController::class, 'store'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('daily-check-ups.store');

    Route::get('/daily-check-ups/{id}', [DailyCheckUpController::class, 'show'])
        ->name('daily-check-ups.show');

    Route::get('/daily-check-ups/{id}/edit', [DailyCheckUpController::class, 'edit'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('daily-check-ups.edit');

    Route::put('/daily-check-ups/{id}', [DailyCheckUpController::class, 'update'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('daily-check-ups.update');

    // Health Alerts
    Route::get('/health-alerts', [HealthAlertController::class, 'index'])->name('health-alerts.index');
    Route::get('/health-alerts/{id}', [HealthAlertController::class, 'show'])->name('health-alerts.show');

    // Follow-ups
    Route::post('/follow-ups', [FollowUpController::class, 'store'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('follow-ups.store');

    Route::put('/follow-ups/{id}', [FollowUpController::class, 'update'])
        ->middleware('role:admin,hse,doctor,manager')
        ->name('follow-ups.update');

    // User Management (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});
