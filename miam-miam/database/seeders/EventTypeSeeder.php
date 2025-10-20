<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventTypeSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('Event_Type')->insert([
            [
                'event_type_id' => 'EVT001',
                'event_type_name' => 'Promotion Spéciale'
            ],
            [
                'event_type_id' => 'EVT002',
                'event_type_name' => 'Mini-Jeu Concours'
            ],
            [
                'event_type_id' => 'EVT003',
                'event_type_name' => 'Événement Social'
            ],
        ]);
    }
}