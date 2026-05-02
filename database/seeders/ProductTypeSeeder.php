<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_types')->insert([
            ['id' => 1, 'product_type' => 'BEVERAGE_ALCOHOLIC_BEER', 'created_at' => '2022-11-18 12:25:04', 'updated_at' => '2022-11-18 12:25:04', 'deleted_at' => null],
            ['id' => 2, 'product_type' => 'BEVERAGE_ALCOHOLIC_WINE', 'created_at' => '2023-03-24 11:58:46', 'updated_at' => '2023-03-24 11:58:46', 'deleted_at' => null],
            ['id' => 4, 'product_type' => 'BEVERAGE_ALCOHOLIC_HARD_SELTZERS_AND_ALTERNATIVES', 'created_at' => '2023-03-24 11:58:46', 'updated_at' => '2023-03-24 11:58:46', 'deleted_at' => null],
            ['id' => 5, 'product_type' => 'HARD_CIDER', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 6, 'product_type' => 'SAKE', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 7, 'product_type' => 'SHOCHU', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 8, 'product_type' => 'BEVERAGE_NON-ALCOHOLIC_SOFT_DRINKS', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 9, 'product_type' => 'BEVERAGE_NON-ALCOHOLIC_COFFEE', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 10, 'product_type' => 'BEVERAGE_NON-ALCOHOLIC_TEA', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 11, 'product_type' => 'BEVERAGE_NON-ALCOHOLIC_WATER_AND_SELTZER', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 12, 'product_type' => 'JUICE', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 13, 'product_type' => 'POWDERED_DRINK_MIXES', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 14, 'product_type' => 'SPORTS_ENERGY_AND_ELECTROLYTE_DRINKS', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 15, 'product_type' => 'MILK_EGGNOG_AND_BUTTERMILK', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 16, 'product_type' => 'MEAL_REPLACEMENTS_AND_PROTEIN_DRINKS', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 17, 'product_type' => 'BEVERAGE_NON-ALCOHOLIC_WINE', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 18, 'product_type' => 'BEVERAGE_NON-ALCOHOLIC_BEER', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 19, 'product_type' => 'BEVERAGE_NON-ALCOHOLIC_SPIRITS', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
            ['id' => 20, 'product_type' => 'NON-ALCOHOLIC_COCKTAIL_MIXERS', 'created_at' => '2023-03-24 11:58:47', 'updated_at' => '2023-03-24 11:58:47', 'deleted_at' => null],
        ]);
    }
}
