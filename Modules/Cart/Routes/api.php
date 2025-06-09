<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\Api\CartControllerInterface;
use Modules\Cart\Http\Controllers\Api\CartItemControllerInterface;

Route::middleware(['api'])
    ->prefix('/carts')
    ->group(function () {
        Route::post('/list', [CartControllerInterface::class, 'list'])->name('api.carts.list');
        Route::get('/init', [CartControllerInterface::class, 'init'])->name('api.carts.init');
        Route::get('/totalItems', [CartControllerInterface::class, 'totalItems'])->name('api.carts.totalItems');
    });

Route::middleware('api')
    ->prefix('carts-items')
    ->group(function () {
        
        Route::post('/', [CartItemControllerInterface::class, 'store'])->name('api.carts-items.store');
        Route::put('/{id}', [CartItemControllerInterface::class, 'update'])->name('api.carts-items.update');
        Route::delete('/{id}', [CartItemControllerInterface::class, 'destroy'])->name('api.carts-items.destroy');
    });
