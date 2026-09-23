<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'superadmin@gmail.com',
                'mobile_number' => '09156608875',
                'first_name' => 'Super',
                'middle_name' => null,
                'last_name' => 'Admin',
                'username' => 'superadmin',
                'name' => 'Super Admin',
                'birthdate' => '2002-03-01',
                'gender' => 'Male',
                'address' => 'Davao City',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'status' => 'approved',
                'account_status' => 'active',
                'email_verified_at' => now(),
                'approved_at' => now(),
                'approved_by' => null,
            ]
        );
    }
}
