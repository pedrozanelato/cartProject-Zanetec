<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\Web\OrderController;

Route::middleware(['web'])
    ->prefix('pedido')
    ->group(function () {
        Route::get('/', [OrderController::class, 'index'])
            ->name('web.orders.index');
});
