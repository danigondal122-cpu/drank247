<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('admins')->insert([
      'name' => 'Super Admin',
      'email' => 'superadmin@247drank.nl',
      'password' => Hash::make('123456'),
      'admin_type' => 'superadmin'
    ]);

    DB::table('customer_services')->insert([
      'cs_name' => 'Customer Service 1',
      'cs_email' => 'cs@247drank.nl',
      'password' => Hash::make('123456'),
      "cs_mobileno" => "089777888999",
      "cs_phone" => "089777888999",
      "cs_street" => "undis",
      "cs_city" => "Banglipur",
      "cs_state" => "Hindia",
      "cs_postcode" => "123456",
      "cs_image" => "123456",
      "is_verified" => "online",
    ]);
  }
}
