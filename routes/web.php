<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Accessible by All Roles (Admin, Staff, Owner)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Master Data Routes (Admin & Staff)
    Route::middleware('role:admin,staff')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('items', ItemController::class);

        // Stock Movement Routes
        Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
        Route::get('/stock-movements/in/create', [StockMovementController::class, 'createIn'])->name('stock-movements.in.create');
        Route::post('/stock-movements/in', [StockMovementController::class, 'storeIn'])->name('stock-movements.in.store');
        Route::get('/stock-movements/out/create', [StockMovementController::class, 'createOut'])->name('stock-movements.out.create');
        Route::post('/stock-movements/out', [StockMovementController::class, 'storeOut'])->name('stock-movements.out.store');
        Route::get('/stock-movements/{stockMovement}', [StockMovementController::class, 'show'])->name('stock-movements.show');
    });

    // Reports Routes (Admin & Owner)
    Route::middleware('role:admin,owner')->group(function () {
        Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
        Route::get('/reports/stock/export', [ReportController::class, 'exportStockCsv'])->name('reports.stock.export');
        Route::get('/reports/transactions', [ReportController::class, 'transactionReport'])->name('reports.transactions');
        Route::get('/reports/transactions/export', [ReportController::class, 'exportTransactionsCsv'])->name('reports.transactions.export');
    });

    // Admin Only Routes (User Management)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
