<?php

namespace Modules\Cart\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

interface CartControllerInterface
{
    public function list(FormRequest $request): JsonResponse;
    public function init(): JsonResponse;
    public function totalItems(FormRequest $request): JsonResponse;
}
