<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemTypeSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('Item_type')->insert([
            [
                'item_type_id' => 'ITP001',
                'item_type_name' => 'Plat Principal'
            ],
            [
                'item_type_id' => 'ITP002',
                'item_type_name' => 'Boisson Fraîche'
            ],
            [
                'item_type_id' => 'ITP003',
                'item_type_name' => 'Dessert'
            ],
            [
                'item_type_id' => 'ITP004',
                'item_type_name' => 'Petit Déjeuner'
            ],
        ]);
    }
}
