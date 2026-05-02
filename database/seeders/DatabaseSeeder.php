<?php

namespace Database\Seeders;

use App\Models\DeliveryPerson;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ModuleSeeder::class,
            ProductTypeSeeder::class,
            SettingsSeeder::class,
            DeliveryTimeScheduleSeeder::class,
            CMSPageSeeder::class,
            PaymentMethodSeeder::class,
            WarehouseSeeder::class,
            AllergenSeeder::class,
            CategoriesSeeder::class,
            PoolsSeeder::class,
            FranchisesSeeder::class,
            ProductsSeeder::class,
            DeliveryPersonSeeder::class,
        ]);
    }
}
