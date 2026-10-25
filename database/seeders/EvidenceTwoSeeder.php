<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Order;

class EvidenceTwoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insertar el rol obligatorio en la tabla 'roles'
        DB::table('roles')->insertOrIgnore([
            'id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Crear usuario de prueba
        $user = User::create([
            'role_id' => 1,
            'username' => 'rmonter',
            'email' => 'ricardo.admin@halcon.com',
            'password_hash' => Hash::make('password123'),
            'is_active' => true,
        ]);

        // 3. Crear cliente de prueba
        $customer = Customer::create([
            'company_name' => 'Tecmilenio Corp',
            'fiscal_data' => 'TEC123456789',
            'delivery_address' => 'Av. Universidad 123',
        ]);

        // 4. Crear orden inicial de prueba
        Order::create([
            'invoice_number' => 'INV-2026-001',
            'customer_number' => $customer->id,
            'created_by_user_id' => $user->id,
            'order_date' => now()->toDateString(),
            'notes' => 'Orden de prueba para evaluación de API REST',
            'current_status' => 'pending',
        ]);
    }
}
