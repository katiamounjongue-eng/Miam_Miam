<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Bill;
use App\Models\OrderHistoric;

class TransactionSeeder extends Seeder
{
   
    public function run(): void
    {
        
        
        //  Récupérer toutes les commandes qui n'ont pas encore de facture/historique
        $orders = Order::all();

        foreach ($orders as $order) {
            // Créer une facture pour chaque commande
            Bill::factory()->create([
                'order_id' => $order->order_id,
                // Le total_cost doit être calculé à partir de order_item + frais de livraison
                'total_cost' => $order->total_cost ?? 5000, 
            ]);

            // Créer un historique de commande pour chaque commande
            OrderHistoric::factory()->create([
                'order_id' => $order->order_id,
                'user_id' => $order->user_id,
            ]);
        }
    }
}