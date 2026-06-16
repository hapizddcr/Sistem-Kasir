<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

// Breeze profile routes (used by auth tests)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Auth\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Auth\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\Auth\ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // POS
    Route::get('/pos', [SaleController::class, 'pos'])->name('sales.pos');
    Route::post('/sales/preview', [SaleController::class, 'previewCart'])->name('sales.preview');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');

    // Sales
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::post('/sales/{sale}/refund', [SaleController::class, 'refund'])->name('sales.refund');

    // Products
    Route::resource('products', ProductController::class);
    Route::get('/products/{product}/barcode', [ProductController::class, 'barcode'])->name('products.barcode');
    Route::get('/api/products/search', [ProductController::class, 'searchApi'])->name('products.search');

    // Categories, Units, Memberships (master data)
    Route::prefix('master')->name('master.')->group(function () {
        Route::get('/categories', [MasterDataController::class, 'categories'])->name('categories');
        Route::post('/categories', [MasterDataController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{category}', [MasterDataController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{category}', [MasterDataController::class, 'destroyCategory'])->name('categories.destroy');

        Route::get('/units', [MasterDataController::class, 'units'])->name('units');
        Route::post('/units', [MasterDataController::class, 'storeUnit'])->name('units.store');
        Route::put('/units/{unit}', [MasterDataController::class, 'updateUnit'])->name('units.update');
        Route::delete('/units/{unit}', [MasterDataController::class, 'destroyUnit'])->name('units.destroy');

        Route::get('/memberships', [MasterDataController::class, 'memberships'])->name('memberships');
        Route::post('/memberships', [MasterDataController::class, 'storeMembership'])->name('memberships.store');
        Route::put('/memberships/{membership}', [MasterDataController::class, 'updateMembership'])->name('memberships.update');
        Route::delete('/memberships/{membership}', [MasterDataController::class, 'destroyMembership'])->name('memberships.destroy');
    });

    // Customers
    Route::resource('customers', CustomerController::class);
    Route::get('/api/customers/search', [CustomerController::class, 'searchApi'])->name('customers.search');

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Stock
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('/history', [StockController::class, 'history'])->name('history');
        Route::get('/low', [StockController::class, 'lowStock'])->name('low');
        Route::get('/adjustments', [StockController::class, 'adjustments'])->name('adjustments');
        Route::get('/adjustments/create', [StockController::class, 'createAdjustment'])->name('create-adjustment');
        Route::post('/adjustments', [StockController::class, 'storeAdjustment'])->name('store-adjustment');
        Route::post('/in', [StockController::class, 'stockIn'])->name('in');
        Route::post('/out', [StockController::class, 'stockOut'])->name('out');
    });

    // Purchases
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update', 'destroy']);
    Route::post('/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/daily', [ReportController::class, 'daily'])->name('daily');
        Route::get('/monthly', [ReportController::class, 'monthly'])->name('monthly');
        Route::get('/top-products', [ReportController::class, 'topProducts'])->name('top-products');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/by-category', [ReportController::class, 'salesByCategory'])->name('by-category');
        Route::get('/export/sales', [ReportController::class, 'exportSales'])->name('export.sales');
    });

    // Users (admin only)
    Route::resource('users', UserController::class)->middleware('role:admin');
});

require __DIR__ . '/auth.php';
