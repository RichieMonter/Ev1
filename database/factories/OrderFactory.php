<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use App\Models\User;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_number' => Customer::factory(),
            'created_by_user_id' => User::inRandomOrder()->first()?->id ?? 1,
            'order_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'notes' => $this->faker->sentence(),
            'current_status' => $this->faker->randomElement(['Pending', 'In Transit', 'Delivered', 'Cancelled']),
            'is_deleted' => false,
        ];
    }
}