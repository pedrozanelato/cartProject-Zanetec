<?php

namespace Modules\Product\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Product\Services\ProductService;
use Modules\Product\DTO\ProductFilterDTO;
use Modules\Product\Entities\Product;
use App\Exceptions\ExceptionWithData;
use Illuminate\Http\Response;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductService();

        Product::factory()->count(5)->create();
    }

    public function test_list_with_pagination_returns_paginated_data()
    {
        $dto = new ProductFilterDTO(
            name: null,
            unitPrice: null,
            id: null,
            perPage: 2,
            page: 1,
            withPaginate: true
        );

        $result = $this->service->list($dto);

        $this->assertNotEmpty($result->pages);
        $this->assertCount(2, $result->data);
        $this->assertIsArray($result->data->resource->toArray());
    }

    public function test_show_existing_product()
    {
        $product = Product::factory()->create([
            'name' => 'Teste Produto',
            'unit_price' => 123.45,
            'stock' => 10,
            'file' => 'teste.jpg'
        ]);

        $result = $this->service->show($product->id);

        $this->assertArrayHasKey('name', $result->data->resolve());
        $this->assertEquals('Teste Produto', $result->data->resolve()['name']);
    }
}
