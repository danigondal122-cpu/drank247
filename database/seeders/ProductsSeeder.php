<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'product_name' => 'Product A',
                'product_article_number' => 'ART001',
                'product_type' => 0,
                'image' => 'product_a.jpg',
                'product_price' => 100.00,
                'vat' => 10,
                'vat_price' => 10.00,
                'category_id' => 4,
                'description' => 'Description for Product A',
                'alcohol' => 'No',
                'order_from' => 0,
                'is_popular' => true,
                'is_show' => true,
                'product_order' => 1,
                'api_available_stock' => 50,
                'main_price' => '120.00',
                'drank247_price' => 110.00,
                'customer_price' => 105.00,
                'franchise_price' => 95.00,
                'alcoholic_items' => 0,
                'product_type_id' => 1,
            ],
            [
                'product_name' => 'Product B',
                'product_article_number' => 'ART002',
                'product_type' => 1, // Extra product
                'image' => 'product_b.jpg',
                'product_price' => 150.00,
                'vat' => 10,
                'vat_price' => 15.00,
                'category_id' => 14,
                'description' => 'Description for Product B',
                'alcohol' => 'Yes',
                'order_from' => 1,
                'is_popular' => false,
                'is_show' => true,
                'product_order' => 2,
                'api_available_stock' => 30,
                'main_price' => '160.00',
                'drank247_price' => 155.00,
                'customer_price' => 150.00,
                'franchise_price' => 140.00,
                'alcoholic_items' => 1,
                'product_type_id' => 2,
            ],
        ]);
    }
}
