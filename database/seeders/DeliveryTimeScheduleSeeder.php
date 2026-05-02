<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryTimeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('delivery_time_schedules')->insert([
            [
                'id' => 1,
                'day' => 'Sunday',
                'start_time_0' => '18',
                'start_time_1' => '00',
                'end_time_0' => '07',
                'end_time_1' => '00',
                'is_checked' => 1,
                'created_at' => '2020-10-20 18:19:36',
                'updated_at' => '2024-01-11 15:50:41',
            ],
            [
                'id' => 2,
                'day' => 'Monday',
                'start_time_0' => '18',
                'start_time_1' => '00',
                'end_time_0' => '07',
                'end_time_1' => '00',
                'is_checked' => 1,
                'created_at' => '2020-10-20 18:20:05',
                'updated_at' => '2024-01-11 15:50:42',
            ],
            [
                'id' => 3,
                'day' => 'Tuesday',
                'start_time_0' => '18',
                'start_time_1' => '00',
                'end_time_0' => '07',
                'end_time_1' => '00',
                'is_checked' => 1,
                'created_at' => '2020-10-20 18:20:28',
                'updated_at' => '2024-01-11 15:50:44',
            ],
            [
                'id' => 4,
                'day' => 'Wednesday',
                'start_time_0' => '18',
                'start_time_1' => '00',
                'end_time_0' => '07',
                'end_time_1' => '00',
                'is_checked' => 1,
                'created_at' => '2020-10-20 18:20:28',
                'updated_at' => '2024-01-11 15:50:44',
            ],
            [
                'id' => 5,
                'day' => 'Thursday',
                'start_time_0' => '18',
                'start_time_1' => '00',
                'end_time_0' => '07',
                'end_time_1' => '00',
                'is_checked' => 1,
                'created_at' => '2020-10-20 18:20:28',
                'updated_at' => '2024-01-11 15:50:46',
            ],
            [
                'id' => 6,
                'day' => 'Friday',
                'start_time_0' => '18',
                'start_time_1' => '00',
                'end_time_0' => '07',
                'end_time_1' => '00',
                'is_checked' => 1,
                'created_at' => '2020-10-20 18:20:28',
                'updated_at' => '2024-01-11 15:50:47',
            ],
            [
                'id' => 7,
                'day' => 'Saturday',
                'start_time_0' => '18',
                'start_time_1' => '00',
                'end_time_0' => '07',
                'end_time_1' => '00',
                'is_checked' => 1,
                'created_at' => '2020-10-20 18:20:28',
                'updated_at' => '2024-01-11 15:50:48',
            ],
        ]);
    }
}
