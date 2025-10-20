<?php

namespace Database\Factories;

use App\Models\SpecialEvent;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EventType;

class SpecialEventFactory extends Factory
{
    protected $model = SpecialEvent::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+' . rand(1, 10) . ' days');
        
        return [
            
            'event_type_id' => EventType::inRandomOrder()->first()?->event_type_id,
            'event_name' => $this->faker->unique()->catchPhrase(),
            'event_starting_date' => $startDate->format('Y-m-d'),
            'event_ending_date' => $endDate->format('Y-m-d'),
            'event_description' => $this->faker->unique()->paragraph(1),
        ];
    }
}
