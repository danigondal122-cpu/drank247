<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert data
        DB::table('payment_methods')->insert([
            ['id' => 1, 'method_name' => 'cod', 'status' => 1, 'created_at' => '2022-02-02 17:40:31', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 2, 'method_name' => 'pin', 'status' => 1, 'created_at' => '2021-08-11 17:17:00', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 3, 'method_name' => 'credit_card', 'status' => 0, 'created_at' => '2021-08-11 17:17:00', 'updated_at' => '2022-05-20 19:57:01'],
            ['id' => 4, 'method_name' => 'paypal', 'status' => 0, 'created_at' => '2022-02-02 18:52:56', 'updated_at' => '2023-08-18 13:03:52'],
            ['id' => 5, 'method_name' => 'ideal', 'status' => 1, 'created_at' => '2022-02-02 18:53:33', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 6, 'method_name' => 'bitpay', 'status' => 0, 'created_at' => '2022-02-02 18:53:33', 'updated_at' => '2023-08-18 13:04:56'],
            ['id' => 7, 'method_name' => 'gpay', 'status' => 0, 'created_at' => '2021-08-11 17:17:00', 'updated_at' => '2022-04-14 11:56:41'],
            ['id' => 8, 'method_name' => 'bancontact', 'status' => 1, 'created_at' => '2023-04-27 13:06:47', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 9, 'method_name' => 'giropay', 'status' => 1, 'created_at' => '2023-04-27 13:06:47', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 10, 'method_name' => 'sofort_banking', 'status' => 1, 'created_at' => '2023-04-27 13:06:47', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 11, 'method_name' => 'trustly', 'status' => 1, 'created_at' => '2023-04-27 13:06:47', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 12, 'method_name' => 'eps_uberweisung', 'status' => 1, 'created_at' => '2023-04-27 13:06:47', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 13, 'method_name' => 'przelewy24', 'status' => 1, 'created_at' => '2023-04-27 13:06:47', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 14, 'method_name' => 'idin', 'status' => 1, 'created_at' => '2023-04-27 13:06:47', 'updated_at' => '2023-08-21 10:57:15'],
            ['id' => 15, 'method_name' => 'klarna', 'status' => 1, 'created_at' => '2023-05-24 05:12:02', 'updated_at' => '2023-08-21 10:57:15'],
        ]);
    }
}
