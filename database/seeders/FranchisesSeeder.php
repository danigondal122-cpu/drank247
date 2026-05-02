<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FranchisesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('franchises')->insert([
            [
                'franchises_name' => 'Franchise A',
                'franchises_no' => 'FR001',
                'image' => 'image_a.jpg',
                'franchises_email' => 'franchise_a@example.com',
                'password' => bcrypt('password123'),
                'reset_token' => null,
                'franchises_username' => 'franchise_a_user',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'mobile_no' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'company_name' => 'Company A',
                'house_no_street' => '123 Main St',
                'block_no' => 'Block A',
                'post_code' => '12345',
                'residence' => 'City A',
                'landmark' => 'Near Park',
                'bank_account' => '123456789',
                'per_day_charges' => 50.00,
                'royalty' => '5%',
                'start_from_date' => '2022-01-01',
                'fs_on_off' => 'online',
                'bank_pass_no' => 'BP123456',
                'bank_pass_front' => 'bank_pass_front_a.jpg',
                'bank_pass_back' => 'bank_pass_back_a.jpg',
                'statement_conduct' => 'statement_a.pdf',
                'licence_front' => 'licence_front_a.jpg',
                'licence_back' => 'licence_back_a.jpg',
                'franchise_contract' => 'contract_a.pdf',
                'extra_option' => 'Extra Option A',
                'payroll_contract' => 1,
                'franchise_number' => 'F001',
                'city' => 'City A',
                'country' => 'Country A',
            ],
            [
                'franchises_name' => 'Franchise B',
                'franchises_no' => 'FR002',
                'image' => 'image_b.jpg',
                'franchises_email' => 'franchise_b@example.com',
                'password' => bcrypt('password123'),
                'reset_token' => null,
                'franchises_username' => 'franchise_b_user',
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'mobile_no' => '0987654321',
                'date_of_birth' => '1992-02-02',
                'company_name' => 'Company B',
                'house_no_street' => '456 Elm St',
                'block_no' => 'Block B',
                'post_code' => '54321',
                'residence' => 'City B',
                'landmark' => 'Near School',
                'bank_account' => '987654321',
                'per_day_charges' => 60.00,
                'royalty' => '6%',
                'start_from_date' => '2022-02-01',
                'fs_on_off' => 'offline',
                'bank_pass_no' => 'BP654321',
                'bank_pass_front' => 'bank_pass_front_b.jpg',
                'bank_pass_back' => 'bank_pass_back_b.jpg',
                'statement_conduct' => 'statement_b.pdf',
                'licence_front' => 'licence_front_b.jpg',
                'licence_back' => 'licence_back_b.jpg',
                'franchise_contract' => 'contract_b.pdf',
                'extra_option' => 'Extra Option B',
                'payroll_contract' => 2,
                'franchise_number' => 'F002',
                'city' => 'City B',
                'country' => 'Country B',
            ],
        ]);
    }
}
