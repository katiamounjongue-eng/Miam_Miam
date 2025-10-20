<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventType;
use App\Models\SpecialEvent;

class SpecialEventSeeder extends Seeder
{
    public function run(): void
    {
        $promoType = EventType::where('event_type_name', 'Promotion Spéciale')->first();

        if ($promoType) {
            // Crée des événements passés et futurs pour les tests
            SpecialEvent::factory()->count(2)->create([
                'event_type_id' => $promoType->event_type_id,
                'event_starting_date' => now()->addDays(5),
                'event_ending_date' => now()->addDays(10),
            ]);

            SpecialEvent::factory()->count(1)->create([
                'event_type_id' => $promoType->event_type_id,
                'event_starting_date' => now()->subDays(10),
                'event_ending_date' => now()->subDays(5),
            ]);
        }
    }
}