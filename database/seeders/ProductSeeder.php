<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Beres Minyak Goreng 2L',
                'description' => 'Minyak goreng kemasan 2 liter, cocok untuk dapur rumah tangga.',
                'price' => 32000,
                'stock' => 120,
                'image_url' => 'https://picsum.photos/seed/oil/400/300',
            ],
            [
                'name' => 'Beras Premium 5kg',
                'description' => 'Beras pulen kualitas premium, 5 kg.',
                'price' => 75000,
                'stock' => 80,
                'image_url' => 'https://picsum.photos/seed/rice/400/300',
            ],
            [
                'name' => 'Indomie Goreng 40pcs',
                'description' => 'Paket indomie goreng isi 40 bungkus.',
                'price' => 125000,
                'stock' => 45,
                'image_url' => 'https://picsum.photos/seed/noodle/400/300',
            ],
            [
                'name' => 'Teh Botol Sosro 350ml x12',
                'description' => 'Teh melati kemasan botol, isi 12 botol.',
                'price' => 48000,
                'stock' => 200,
                'image_url' => 'https://picsum.photos/seed/tea/400/300',
            ],
            [
                'name' => 'Sabun Cuci Piring 650ml',
                'description' => 'Sabun cuci piring lemon, botol 650 ml.',
                'price' => 18500,
                'stock' => 150,
                'image_url' => 'https://picsum.photos/seed/soap/400/300',
            ],
            [
                'name' => 'Kopi Kapal Api Special 165g',
                'description' => 'Kopi bubuk premium untuk sarapan.',
                'price' => 22000,
                'stock' => 90,
                'image_url' => 'https://picsum.photos/seed/coffee/400/300',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}
