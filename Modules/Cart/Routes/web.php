<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\Web\CartController;

Route::middleware(['web'])
    ->prefix('carrinho')
    ->group(function () {
        Route::get('/', [CartController::class, 'index'])
            ->name('web.carts.index');
});
