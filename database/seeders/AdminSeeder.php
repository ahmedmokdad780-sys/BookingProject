<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {

        if (User::where('account_type', 'admin')->exists()) {
            return;
        }

        User::create([
            'name' => 'System',
            'last_name' => 'Admin',
            'phone' => '0500000000',
            'password' => Hash::make('1234567890'),
            'birthdate' => '1990-01-01',
            'account_type' => 'admin',
            'status' => 'approved',
            'is_active' => true,
            'national_id_image' => 'default.png',
            'personal_image' => 'default.png',
        ]);
    }
}
