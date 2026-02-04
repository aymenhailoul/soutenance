<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ChargeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\FacturationController;
use App\Http\Controllers\FacturationServicesController;
use App\Http\Controllers\FacturationProduitsController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\VehicleController;


Route::middleware(['auth', 'page.permission'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/_debug/auth', function () {
        return response()->json([
            'auth_check' => auth()->check(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'session_driver' => config('session.driver'),
        ]);
    })->name('debug.auth');

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

    // Vehicle routes
    Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
    Route::get('/vehicles/{vehicle}/history', [VehicleController::class, 'history'])->name('vehicles.history');
    Route::get('/vehicles/search', [VehicleController::class, 'search'])->name('vehicles.search');
    // Stock routes

    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/search-products', [StockController::class, 'searchProducts'])->name('stock.search-products');
    Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
    Route::post('/stock/bulk', [StockController::class, 'storeBulk'])->name('stock.store-bulk');
    Route::get('/stock/movements', [StockController::class, 'movements'])->name('stock.movements');
    Route::get('/stock/movements/export', [StockController::class, 'exportMovements'])->name('stock.movements.export');
    // Products routes

    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/export/{format}', [ProductController::class, 'export'])
        ->whereIn('format', ['xlsx'])
        ->name('products.export');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Facturation Services routes
    Route::get('/facturation/services', [FacturationServicesController::class, 'index'])->name('facturation.services.index');
    Route::get('/facturation/services/search-services', [FacturationServicesController::class, 'searchServices'])->name('facturation.services.search');
    Route::get('/facturation/services/search-clients', [FacturationServicesController::class, 'searchClients'])->name('facturation.services.search-clients');
    Route::post('/facturation/services', [FacturationServicesController::class, 'store'])->name('facturation.services.store');

    // Facturation Produits routes
    Route::get('/facturation/produits', [FacturationProduitsController::class, 'index'])->name('facturation.produits.index');
    Route::get('/facturation/produits/search-products', [FacturationProduitsController::class, 'searchProducts'])->name('facturation.produits.search');
    Route::get('/facturation/produits/search-clients', [FacturationProduitsController::class, 'searchClients'])->name('facturation.produits.search-clients');
    Route::post('/facturation/produits', [FacturationProduitsController::class, 'store'])->name('facturation.produits.store');

    // Common Facturation routes (PDF, Cancel)
    Route::get('/facturation/{invoice}/pdf', [FacturationController::class, 'downloadPdf'])->name('facturation.pdf');
    Route::post('/facturation/{invoice}/cancel', [FacturationController::class, 'cancel'])->name('facturation.cancel');
    
    // Ventes routes
    Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index');
    Route::get('/ventes/{invoice}', [VenteController::class, 'show'])->name('ventes.show');
    Route::get('/ventes/export/excel', [VenteController::class, 'export'])->name('ventes.export');

    // Credit Notes (Returns/Avoir) routes
    Route::get('/retours', [CreditNoteController::class, 'index'])->name('retours.index');
    Route::get('/retours/export/excel', [CreditNoteController::class, 'export'])->name('retours.export');
    Route::get('/retours/{creditNote}/details', [CreditNoteController::class, 'show'])->name('retours.show');
    Route::post('/credit-notes', [CreditNoteController::class, 'store'])->name('credit-notes.store');
    Route::get('/credit-notes/{creditNote}/pdf', [CreditNoteController::class, 'downloadPdf'])->name('credit-notes.pdf');

    // Employee routes
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/export/{format}', [EmployeeController::class, 'export'])
        ->whereIn('format', ['xlsx'])
        ->name('employees.export');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Charge routes
    Route::get('/charges', [ChargeController::class, 'index'])->name('charges.index');
    Route::post('/charges', [ChargeController::class, 'store'])->name('charges.store');
    Route::get('/charges/export', [ChargeController::class, 'export'])->name('charges.export');

    // Test route
    Route::get('/meeting', function () {
        return view('meeting');
    })->name('meeting.index');


});



require __DIR__ . '/auth.php';
