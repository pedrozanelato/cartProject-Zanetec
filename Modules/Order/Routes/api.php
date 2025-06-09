<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Api\OrderControllerInterface;

Route::middleware(['api'])
    ->prefix('/orders')
    ->group(function () {
        Route::post('/', [OrderControllerInterface::class, 'order'])->name('api.orders.order');
    });