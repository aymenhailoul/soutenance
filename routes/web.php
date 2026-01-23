<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('dashboard');

    Route::get('/_debug/auth', function () {
        return response()->json([
            'auth_check' => auth()->check(),
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'session_driver' => config('session.driver'),
        ]);
    })->name('debug.auth');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/search-products', [StockController::class, 'searchProducts'])->name('stock.search-products');
    Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
    Route::get('/stock/movements', [StockController::class, 'movements'])->name('stock.movements');
    Route::get('/stock/movements/export', [StockController::class, 'exportMovements'])->name('stock.movements.export');

    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/export/{format}', [ProductController::class, 'export'])
    ->whereIn('format', ['xlsx'])
    ->name('products.export');

Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');


});



require __DIR__.'/auth.php';
