<?php

use Illuminate\Support\Facades\Route;

use Modules\Product\Http\Controllers\Web\ProductController;

Route::middleware(['web'])
    ->prefix('produtos')
    ->group(function () {
        Route::get('/', [ProductController::class, 'index'])
            ->name('web.products.index');
});