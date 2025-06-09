<?php

namespace Modules\Product\Http\Controllers\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Product\Entities\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_list_products_default_pagination()
    {
        Product::factory()->count(3)->create();

        $response = $this->postJson(route('api.products.list'));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'success',
                'code',
                'data' => [
                    '*' => ['id','name','unitPrice','file','createdAt','updatedAt']
                ],
                'pages'
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_list_products_without_pagination()
    {
        Product::factory()->count(4)->create();

        $payload = ['withPaginate' => false];
        $response = $this->postJson(route('api.products.list'), $payload);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonMissing(['pages'])
            ->assertJsonStructure([
                'success','code','data'
            ]);

        $this->assertCount(4, $response->json('data'));
    }

    public function test_show_product_success()
    {
        $product = Product::factory()->create([
            'file' => 'products/img.jpg'
        ]);

        $url = Storage::disk('public')->url($product->file);

        $response = $this->getJson(route('products.show', $product->id));

        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('data.id', $product->id)
                 ->assertJsonPath('data.file', $url);
    }

    public function test_show_product_not_found()
    {
        $response = $this->getJson(route('products.show', 999));

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE)
            ->assertJson(['message' => 'Produto não encontrada.']);
    }
}
