<?php

namespace Database\Factories;

use App\Models\Bill;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Payment;

class BillFactory extends Factory
{
    protected $model = Bill::class;

    public function definition(): array
    {
        return [
            // 'bill_id' sera géré par Trait/Seeder
            'order_id' => null, // Doit être écrasé dans le Seeder
            'total_cost' => $this->faker->randomFloat(2, 3000, 15000),
            'payment_method_id' => Payment::inRandomOrder()->first()?->payment_method_id,
            'payment_date' => $this->faker->dateTimeBetween('-5 months', 'now'),
        ];
    }
}