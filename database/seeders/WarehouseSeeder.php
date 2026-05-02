<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('warehouses')->insert([
            [
                'id' => 1,
                'wh_name' => 'Bossche wijnkoperij',
                'wh_logo' => '1649693012_FB_IMG_1649693012457.jpg',
                'wh_minprice' => '500',
                'created_at' => '2022-04-16 11:07:20',
                'updated_at' => '2023-02-24 18:02:39',
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'wh_name' => '247WAREHOUSE',
                'wh_logo' => '1649833225_247-Drank-Logo.png',
                'wh_minprice' => '500',
                'created_at' => '2022-04-16 11:07:20',
                'updated_at' => '2022-04-13 05:00:25',
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'wh_name' => 'Anker Spirits Amsterdam',
                'wh_logo' => '1649693283_ankeramsterdamlogo.png',
                'wh_minprice' => '1250',
                'created_at' => '2022-04-16 11:07:20',
                'updated_at' => '2023-02-24 18:03:12',
                'deleted_at' => null,
            ],
            [
                'id' => 10,
                'wh_name' => 'Sligro',
                'wh_logo' => '1649693178_images.png',
                'wh_minprice' => '500',
                'created_at' => '2022-04-11 14:06:18',
                'updated_at' => '2023-05-16 12:27:29',
                'deleted_at' => null,
            ],
        ]);
    }
}
