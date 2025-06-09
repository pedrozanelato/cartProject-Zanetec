<?php

namespace Modules\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Product\Entities\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Smartphone Samsung Galaxy',
            'unit_price' => 1299.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img1.jpg'
        ]);

        Product::create([
            'name' => 'Notebook Dell Inspiron',
            'unit_price' => 2499.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img2.jpg'
        ]);

        Product::create([
            'name' => 'Fone de Ouvido Bluetooth',
            'unit_price' => 199.00,
            'stock' => 1000,
            'file'  => '/products/imgs/img3.jpg'
        ]);

        Product::create([
            'name' => 'Smart TV 55 Polegadas',
            'unit_price' => 1899.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img4.png'
        ]);
        Product::create([
            'name' => 'Tablet iPad Air',
            'unit_price' => 3299.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img5.png'
        ]);

        Product::create([
            'name' => 'Câmera Digital Canon',
            'unit_price' => 2799.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img6.jpg'
        ]);

        Product::create([
            'name' => 'Console PlayStation 5',
            'unit_price' => 4299.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img7.jpg'
        ]);

        Product::create([
            'name' => 'Smartwatch Apple Watch',
            'unit_price' => 2199.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img8.jpg'
        ]);
        Product::create([
            'name' => 'Teclado Mecânico Gamer',
            'unit_price' => 399.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img9.jpg'
        ]);

        Product::create([
            'name' => 'Mouse Gamer RGB',
            'unit_price' => 149.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img10.jpg'
        ]);

        Product::create([
            'name' => 'Monitor 4K 27 Polegadas',
            'unit_price' => 1599.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img11.png'
        ]);

        Product::create([
            'name' => 'Caixa de Som Bluetooth',
            'unit_price' => 299.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img12.jpeg'
        ]);
        
        Product::create([
            'name' => 'Roteador Wi-Fi 6',
            'unit_price' => 599.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img13.jpeg'
        ]);
        Product::create([
            'name' => 'SSD 1TB NVMe',
            'unit_price' => 799.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img14.jpg'
        ]);
        Product::create([
            'name' => 'Placa de Vídeo RTX 4060',
            'unit_price' => 3999.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img15.png'
        ]);
        Product::create([
            'name' => 'Processador Intel i7',
            'unit_price' => 2299.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img16.jpg'
        ]);
        Product::create([
            'name' => 'Memória RAM 32GB',
            'unit_price' => 899.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img17.jpg'
        ]);
        Product::create([
            'name' => 'Cadeira Gamer Ergonômica',
            'unit_price' => 1199.99,
            'stock' => 1000,
            'file'  => '/products/imgs/img18.jpg'
        ]);
    }
}
