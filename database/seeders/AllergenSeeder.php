<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllergenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('allergens')->insert([
            ['id' => 8, 'name' => 'Geen', 'deliverect_value' => 0, 'created_at' => '2021-07-28 18:50:47', 'updated_at' => '2022-01-21 20:11:14', 'deleted_at' => '2022-01-21 20:11:14'],
            ['id' => 10, 'name' => 'Ei-eiwit (meestal in wijn)', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:23:52', 'updated_at' => '2022-01-21 20:11:04', 'deleted_at' => '2022-01-21 20:11:04'],
            ['id' => 11, 'name' => 'Gluten', 'deliverect_value' => 101, 'created_at' => '2021-09-15 18:24:03', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 12, 'name' => 'Druiven', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:24:12', 'updated_at' => '2022-01-21 20:11:40', 'deleted_at' => '2022-01-21 20:11:40'],
            ['id' => 13, 'name' => 'Histamine', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:24:20', 'updated_at' => '2022-01-21 20:11:45', 'deleted_at' => '2022-01-21 20:11:45'],
            ['id' => 14, 'name' => 'Hop', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:24:28', 'updated_at' => '2022-01-21 20:11:51', 'deleted_at' => '2022-01-21 20:11:51'],
            ['id' => 15, 'name' => 'Rogge', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:24:36', 'updated_at' => '2022-01-21 20:12:01', 'deleted_at' => '2022-01-21 20:12:01'],
            ['id' => 16, 'name' => 'Eiwitten uit zeevruchten', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:25:01', 'updated_at' => '2022-01-21 20:12:08', 'deleted_at' => '2022-01-21 20:12:08'],
            ['id' => 17, 'name' => 'Natriummetabisulfiet', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:25:29', 'updated_at' => '2022-01-21 20:12:15', 'deleted_at' => '2022-01-21 20:12:15'],
            ['id' => 18, 'name' => 'Sulfieten', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:25:46', 'updated_at' => '2022-01-21 20:12:23', 'deleted_at' => '2022-01-21 20:12:23'],
            ['id' => 19, 'name' => 'Tarwe', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:25:55', 'updated_at' => '2022-01-21 20:12:37', 'deleted_at' => '2022-01-21 20:12:37'],
            ['id' => 20, 'name' => 'Gist', 'deliverect_value' => 0, 'created_at' => '2021-09-15 18:26:06', 'updated_at' => '2022-01-21 20:11:29', 'deleted_at' => '2022-01-21 20:11:29'],
            ['id' => 21, 'name' => 'Glutenbevattende Granen, Gerst', 'deliverect_value' => 0, 'created_at' => '2021-10-19 21:59:45', 'updated_at' => '2022-01-21 20:10:24', 'deleted_at' => '2022-01-21 20:10:24'],
            ['id' => 22, 'name' => 'Alcohol', 'deliverect_value' => 1, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 23, 'name' => 'Halal', 'deliverect_value' => 2, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 24, 'name' => 'Kosher', 'deliverect_value' => 3, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 25, 'name' => 'Vegan', 'deliverect_value' => 4, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 26, 'name' => 'Vegetarian', 'deliverect_value' => 5, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 27, 'name' => 'Can Serve Alone', 'deliverect_value' => 6, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 28, 'name' => 'Bottle Deposit', 'deliverect_value' => 7, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 29, 'name' => 'Organic', 'deliverect_value' => 8, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 30, 'name' => 'Natural', 'deliverect_value' => 9, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 31, 'name' => 'Celery', 'deliverect_value' => 100, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 32, 'name' => 'Crustaceans', 'deliverect_value' => 102, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 33, 'name' => 'Fish', 'deliverect_value' => 103, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 34, 'name' => 'Eggs', 'deliverect_value' => 104, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 35, 'name' => 'Lupin', 'deliverect_value' => 105, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 36, 'name' => 'Milk', 'deliverect_value' => 106, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 37, 'name' => 'Molluscs', 'deliverect_value' => 107, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 38, 'name' => 'Mustard', 'deliverect_value' => 108, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 39, 'name' => 'Nuts', 'deliverect_value' => 109, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 40, 'name' => 'Peanuts', 'deliverect_value' => 110, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 41, 'name' => 'Sesame', 'deliverect_value' => 111, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 42, 'name' => 'Soya', 'deliverect_value' => 112, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 43, 'name' => 'Sulphites', 'deliverect_value' => 113, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 44, 'name' => 'Almonds', 'deliverect_value' => 114, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 45, 'name' => 'Barley', 'deliverect_value' => 115, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 46, 'name' => 'Brazil Nuts', 'deliverect_value' => 116, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 47, 'name' => 'Cashew', 'deliverect_value' => 117, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 48, 'name' => 'Hazelnuts', 'deliverect_value' => 118, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 49, 'name' => 'Kamut', 'deliverect_value' => 119, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 50, 'name' => 'Macadamia', 'deliverect_value' => 120, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 51, 'name' => 'Oats', 'deliverect_value' => 121, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 52, 'name' => 'Pecan', 'deliverect_value' => 122, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 53, 'name' => 'Pistachios', 'deliverect_value' => 123, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 54, 'name' => 'Rye', 'deliverect_value' => 124, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 55, 'name' => 'Spelt', 'deliverect_value' => 125, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 56, 'name' => 'Walnuts', 'deliverect_value' => 126, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 57, 'name' => 'Wheat', 'deliverect_value' => 127, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 58, 'name' => 'Sugared Drink', 'deliverect_value' => 128, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 59, 'name' => 'No Allergens', 'deliverect_value' => 1000, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 60, 'name' => 'Gluten Free', 'deliverect_value' => 1101, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 61, 'name' => 'Sugar Free', 'deliverect_value' => 1128, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
            ['id' => 62, 'name' => 'Lac Free', 'deliverect_value' => 1129, 'created_at' => '2022-01-19 10:41:09', 'updated_at' => '2022-01-19 10:41:09', 'deleted_at' => null],
        ]);
    }
}
