<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'id' => 1,
                'time_schedule' => 1,
                'email' => 'Info@247drank.com',
                'address' => 'Nederland',
                'email_show' => 0,
                'contact_no' => '00319009290',
                'created_at' => '2020-10-20 18:22:33',
                'updated_at' => '2024-01-11 15:50:38',
            ],
        ]);
    }
}
