<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asegurar que id = 1 sea Administrator y crear los demás roles en department_name
        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            ['department_name' => 'Administrator', 'created_at' => now(), 'updated_at' => now()]
        );

        $otherRoles = ['Sales', 'Purchasing', 'Warehouse', 'Route'];
        foreach ($otherRoles as $roleName) {
            DB::table('roles')->updateOrInsert(
                ['department_name' => $roleName],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $adminRoleId = DB::table('roles')->where('department_name', 'Administrator')->value('id');

        // 2. Activar tu usuario existente rmonter
        DB::table('users')->updateOrInsert(
            ['email' => 'ricardo.admin@halcon.com'],
            [
                'username' => 'rmonter',
                'password_hash' => Hash::make('admin123'),
                'role_id' => $adminRoleId,
                'is_active' => 1,
                'updated_at' => now(),
            ]
        );
    }
}
