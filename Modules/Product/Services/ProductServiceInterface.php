<?php

namespace Modules\Product\Services;

use App\Models\DefaultReturnType;
use Modules\Product\DTO\ProductFilterDTO;

interface ProductServiceInterface
{
    public function list(ProductFilterDTO $productDto): DefaultReturnType;

    public function show(int $id): DefaultReturnType;
}
