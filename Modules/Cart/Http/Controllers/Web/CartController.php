<?php

namespace Modules\Cart\Http\Controllers\Web;

class CartController
{
    public function index()
    {
        return view('cart::index');
    }
}
