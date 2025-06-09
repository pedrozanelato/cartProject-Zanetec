<?php

namespace Modules\Product\Services;

use App\Exceptions\ExceptionWithData;
use App\Models\DefaultReturnType;
use Illuminate\Http\Response;
use Modules\Product\DTO\ProductFilterDTO;
use Modules\Product\Entities\Product;
use Modules\Product\Transformers\ProductResource;

class ProductService implements ProductServiceInterface
{
    public function __construct()
    {
    }
    
    public function list(ProductFilterDTO $productDto): DefaultReturnType
    {
        $productsQuery = Product::query()->orderBy('created_at', 'desc');
            
        $products = $productDto->withPaginate ?
        $productsQuery->paginate(perPage: $productDto->perPage, page: $productDto->page) :
        $productsQuery->get();

        $defaultReturn = DefaultReturnType::create();

        return $productDto->withPaginate  
            ? $defaultReturn->setPages($products)->setData(ProductResource::collection($products->items())) 
            : $defaultReturn->setData(ProductResource::collection($products));
    }

    /**
     * @throws ExceptionWithData
     */
    public function show(int $id): DefaultReturnType
    {
        $product = $this->getById(id: $id);

        return DefaultReturnType::create()
            ->setData(ProductResource::make($product));
    }

    /**
     * @throws ExceptionWithData
     */
    public function getById(int $id): Product
    {
        /**
         * @var Product $product
         */
        $product = Product::query()->find($id);
        
        exception(
            condition: empty($product),
            message: 'Produto não encontrada.',
            code: Response::HTTP_NOT_ACCEPTABLE
        );

        return $product;
    }
}
