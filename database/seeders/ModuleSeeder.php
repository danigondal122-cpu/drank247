<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert([
            ['id' => 1, 'module_name' => 'Category Management', 'created_at' => '2021-08-27 12:18:09', 'updated_at' => '2021-08-27 12:18:09', 'deleted_at' => null],
            ['id' => 2, 'module_name' => 'Product Management', 'created_at' => '2021-08-27 12:18:09', 'updated_at' => '2021-08-27 12:18:09', 'deleted_at' => null],
            ['id' => 3, 'module_name' => 'Extra Product', 'created_at' => '2021-08-27 12:18:21', 'updated_at' => '2021-08-27 12:18:21', 'deleted_at' => null],
            ['id' => 4, 'module_name' => 'Pool Management', 'created_at' => '2021-08-27 12:18:21', 'updated_at' => '2021-08-27 12:18:21', 'deleted_at' => null],
            ['id' => 5, 'module_name' => 'Customer Services', 'created_at' => '2021-08-27 12:18:34', 'updated_at' => '2021-08-27 12:18:34', 'deleted_at' => null],
            ['id' => 6, 'module_name' => 'Franchise Management', 'created_at' => '2021-08-27 12:18:34', 'updated_at' => '2021-08-27 12:18:34', 'deleted_at' => null],
            ['id' => 7, 'module_name' => 'Stock Management', 'created_at' => '2021-08-27 12:18:46', 'updated_at' => '2021-08-27 12:18:46', 'deleted_at' => null],
            ['id' => 8, 'module_name' => 'Customer Management', 'created_at' => '2021-08-27 12:18:46', 'updated_at' => '2021-08-27 12:18:46', 'deleted_at' => null],
            ['id' => 9, 'module_name' => 'Delivery Person', 'created_at' => '2021-08-27 12:18:58', 'updated_at' => '2021-08-27 12:18:58', 'deleted_at' => null],
            ['id' => 10, 'module_name' => 'Order', 'created_at' => '2021-08-27 12:18:58', 'updated_at' => '2021-08-27 12:18:58', 'deleted_at' => null],
            ['id' => 11, 'module_name' => 'Stock Order', 'created_at' => '2021-08-27 12:19:13', 'updated_at' => '2021-08-27 12:19:13', 'deleted_at' => null],
            ['id' => 12, 'module_name' => 'Delivery Schedule', 'created_at' => '2021-08-27 12:19:13', 'updated_at' => '2021-08-27 12:19:13', 'deleted_at' => null],
            ['id' => 13, 'module_name' => 'Promo Code', 'created_at' => '2021-08-27 12:19:25', 'updated_at' => '2021-08-27 12:19:25', 'deleted_at' => null],
            ['id' => 14, 'module_name' => 'Broadcast message', 'created_at' => '2021-08-27 12:19:25', 'updated_at' => '2021-08-27 12:19:25', 'deleted_at' => null],
            ['id' => 15, 'module_name' => 'Allergen', 'created_at' => '2021-08-27 12:19:38', 'updated_at' => '2021-08-27 12:19:38', 'deleted_at' => null],
            ['id' => 16, 'module_name' => 'Settings', 'created_at' => '2021-08-27 12:19:38', 'updated_at' => '2021-08-27 12:19:38', 'deleted_at' => null],
        ]);
    }
}
