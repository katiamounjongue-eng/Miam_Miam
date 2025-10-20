<?php

namespace Database\Factories;

use App\Models\OrderHistoric;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderHistoricFactory extends Factory
{
    protected $model = OrderHistoric::class;

    public function definition(): array
    {
        // Les valeurs seront majoritairement écrasées par le TransactionSeeder
        return [
            // 'historic_id' sera géré par Trait/Seeder
            'order_id' => null, // Doit être écrasé dans le Seeder
            'user_id' => null, // Doit être écrasé dans le Seeder
        ];
    }
}