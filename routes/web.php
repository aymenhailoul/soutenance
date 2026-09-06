<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentAssignmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'page.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Category routes
    Route::resource('categories', CategoryController::class);

    // Equipment routes
    Route::resource('equipment', EquipmentController::class);

    // User management routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Page management routes
    Route::post('/pages', [UserController::class, 'storePage'])->name('pages.store');
    Route::put('/pages/{page}', [UserController::class, 'updatePage'])->name('pages.update');
    Route::delete('/pages/{page}', [UserController::class, 'destroyPage'])->name('pages.destroy');

    // Clients routes
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    // Sites routes
    Route::resource('sites', SiteController::class);

    // Equipment Assignments routes
    Route::get('/assignments', [EquipmentAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/assignments', [EquipmentAssignmentController::class, 'store'])->name('assignments.store');
    Route::post('/assignments/{assignment}/return', [EquipmentAssignmentController::class, 'returnEquipment'])->name('assignments.return');

    // Maintenance routes
    Route::resource('maintenances', MaintenanceController::class);

    // Stock Movements / Consumables routes
    Route::get('/stock', [StockMovementController::class, 'index'])->name('stock.index');
    Route::post('/stock', [StockMovementController::class, 'store'])->name('stock.store');

    // Report & Export routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/equipment', [ReportController::class, 'exportEquipment'])->name('reports.export.equipment');
    Route::get('/reports/export/assignments', [ReportController::class, 'exportAssignments'])->name('reports.export.assignments');

    // Backup & Storage routes
    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
    Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::delete('/backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');

    // Employee routes
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/export/{format}', [EmployeeController::class, 'export'])
        ->whereIn('format', ['xlsx'])
        ->name('employees.export');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});

require __DIR__ . '/auth.php';
