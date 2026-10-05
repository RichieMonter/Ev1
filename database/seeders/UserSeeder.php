<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'role_id' => 1,
            'username' => 'admin_user',
            'password_hash' => Hash::make('password123'),
        ]);

        User::create([
            'role_id' => 2,
            'username' => 'logistics_operator',
            'password_hash' => Hash::make('password123'),
        ]);

        User::create([
            'role_id' => 2,
            'username' => 'sales_rep',
            'password_hash' => Hash::make('password123'),
        ]);
    }
}