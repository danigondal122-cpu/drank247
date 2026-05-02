<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('pools')->insert([
            ['id' => 1, 'from_postcode' => '6901', 'to_postcode' => '7000', 'area' => 'Vijaypark, Nikol', 'delivery_charge' => '2.50', 'delivery_start_from' => '22.50', 'delivery_free_from' => '75', 'created_at' => '2020-09-14 23:54:12', 'updated_at' => '2021-07-21 17:53:48', 'deleted_at' => '2021-07-21 17:53:48'],
            ['id' => 2, 'from_postcode' => '6800', 'to_postcode' => '6900', 'area' => 'Chandkheda', 'delivery_charge' => '2.50', 'delivery_start_from' => '22.50', 'delivery_free_from' => '75', 'created_at' => '2020-10-10 06:59:40', 'updated_at' => '2021-07-21 17:53:51', 'deleted_at' => '2021-07-21 17:53:51'],
            ['id' => 3, 'from_postcode' => '1086', 'to_postcode' => '1086', 'area' => 'Steigereiland', 'delivery_charge' => '2.50', 'delivery_start_from' => '22.50', 'delivery_free_from' => '75', 'created_at' => '2020-12-03 07:15:26', 'updated_at' => '2021-12-08 20:17:01', 'deleted_at' => '2021-12-08 20:17:01'],
            ['id' => 4, 'from_postcode' => '6900', 'to_postcode' => '7000', 'area' => 'Area A', 'delivery_charge' => '2.50', 'delivery_start_from' => '22.50', 'delivery_free_from' => '75', 'created_at' => '2020-12-15 03:51:59', 'updated_at' => '2020-12-31 08:26:56', 'deleted_at' => '2020-12-31 08:26:56'],
            ['id' => 5, 'from_postcode' => '9000', 'to_postcode' => '9800', 'area' => 'Groningen', 'delivery_charge' => '2.50', 'delivery_start_from' => '22.50', 'delivery_free_from' => '75', 'created_at' => '2020-12-25 04:56:26', 'updated_at' => '2021-07-21 17:53:56', 'deleted_at' => '2021-07-21 17:53:56'],
        ]);
    }
}
