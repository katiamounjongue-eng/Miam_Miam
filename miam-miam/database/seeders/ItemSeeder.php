<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemType;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    
    public function run(): void
    {
        $mainDishType = ItemType::where('item_type_name', 'Plat Principal')->first();
        $drinkType = ItemType::where('item_type_name', 'Boisson Fraîche')->first();

        if ($mainDishType && $drinkType) {
            
            // Crée 5 plats principaux
            Item::factory()->count(5)->create([
                'item_type_id' => $mainDishType->item_type_id,
            ]);

            // Crée 3 boissons
            Item::factory()->count(3)->create([
                'item_type_id' => $drinkType->item_type_id,
            ]);
        }
    }
}
