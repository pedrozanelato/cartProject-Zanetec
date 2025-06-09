<?php

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

interface ProductControllerInterface
{
    public function list(FormRequest $request): JsonResponse;

    public function show(int $id): JsonResponse;
}