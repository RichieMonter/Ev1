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
        // ANTES (contraseña literal repetida directamente en cada registro):
        // 'password_hash' => Hash::make('password123'),

        // DESPUÉS (obtenida desde entorno/configuración o variable centralizada y hasheada):
        $seedPassword = Hash::make(env('SEED_USER_PASSWORD', 'HalconPass2026!'));

        User::updateOrCreate(
            ['username' => 'admin_user'],
            [
                'role_id' => 1,
                'password_hash' => $seedPassword,
            ]
        );

        User::updateOrCreate(
            ['username' => 'logistics_operator'],
            [
                'role_id' => 2,
                'password_hash' => $seedPassword,
            ]
        );

        User::updateOrCreate(
            ['username' => 'sales_rep'],
            [
                'role_id' => 2,
                'password_hash' => $seedPassword,
            ]
        );
    }
}