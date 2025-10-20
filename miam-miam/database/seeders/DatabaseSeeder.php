<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    
    public function run(): void
    {
        $this->call([
            // Dépendances de BASE 
            UserTypeSeeder::class,
            OrderStatutSeeder::class, 
            LocalisationSeeder::class, 
            ItemTypeSeeder::class,
            PaymentSeeder::class,

            // Utilisateurs et Contenu 
            UserSeeder::class, 
            ItemSeeder::class,
            SpecialEventSeeder::class,

            // Transactions 
            OrderSeeder::class, 
            TransactionSeeder::class, 

            
        ]);
    }
}
