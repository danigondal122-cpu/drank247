<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('delivery_people')->insert([
            [
                'id' => 1,
                'dp_name' => 'Sunday',
                'dp_email' => 'customer1@247drank.nl',
                'dp_password' => '123456',
                'dp_contact_no' => '089777888999',
                'dp_street' => 'test',
                'dp_city' => "test",
                'dp_state' => "test",
                'dp_postcode' => "234534",
                'dp_start_odometer_number' => "0",
                'dp_stop_odometer_number' => "100",
                'created_at' => '2020-10-20 18:19:36',
                'updated_at' => '2024-01-11 15:50:41',
            ]
        ]);

        DB::table('sub_delivery_people')->insert([
            [
                'id' => 1,
                'delivery_person_id' => 1,
                'franchise_id' => 1,
                'created_at' => '2020-10-20 18:19:36',
                'updated_at' => '2024-01-11 15:50:41',
            ]
        ]);
    }
}
