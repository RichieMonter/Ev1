<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['id' => 1, 'department_name' => 'Administrator']);
        Role::create(['id' => 2, 'department_name' => 'Logistics']);
    }
}