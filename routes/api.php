<?php

use App\Http\Controllers\System\LoginController;
use App\Http\Controllers\Purchasing\SupplierController;
use App\Http\Controllers\Warehouse\WarehouseTransactionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);

Route::get('/double', function () {
    return view('double');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/userinfo', [LoginController::class, 'currentUser'])->name('userinfo');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});