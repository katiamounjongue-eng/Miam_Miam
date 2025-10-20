<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Localisation;
use App\Models\OrderStatut;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        // Récupérer un étudiant 
        $studentUserId = User::where('user_type_id', 'UT003')->inRandomOrder()->first()?->user_id ?? User::inRandomOrder()->first()?->user_id;

        return [
            
            'user_id' => $studentUserId,
            'localisation_id' => Localisation::inRandomOrder()->first()?->localisation_id,
            'order_statut_id' => OrderStatut::inRandomOrder()->first()?->order_statut_id,
            'order_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'points_used' => $this->faker->boolean(20) ? $this->faker->numberBetween(0, 500) : 0, // 20% de chances d'utiliser des points
        ];
    }
}
