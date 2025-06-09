<?php

namespace Modules\Order\Http\Controllers\Web;

class OrderController
{
    public function index()
    {
        return view('order::index');
    }
}
