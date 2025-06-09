<?php

namespace Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Modules\Product\DTO\ProductFilterDTO;

use Modules\Product\Services\ProductServiceInterface;

class ProductController extends Controller implements ProductControllerInterface
{

    public function __construct(
        private readonly ProductServiceInterface $productService
    )
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function list(FormRequest $request): JsonResponse
    {
        return $this->productService->list(ProductFilterDTO::fromRequest($request))->toJsonResponse();
    }
    
    /**
     * Show the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        return $this->productService->show($id)->toJsonResponse();
    }
}
