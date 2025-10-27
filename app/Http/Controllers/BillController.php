<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;

class BillController extends Controller
{
    // la liste des factures 
     
    public function index(Request $request)
    {
        
        $bills = Bill::with(['order.user', 'paymentMethod'])
                     ->orderBy('payment_date', 'desc')
                     ->paginate($request->get('limit', 20)); 

        return response()->json($bills);
    }

    
     
    public function show($id)
    {
        $bill = Bill::with(['order.user', 'paymentMethod', 'order.items.item'])
                    ->findOrFail($id);

        // Calcul du total
        $total = 0;
        foreach ($bill->order->items as $orderitem) {
            $total += $orderitem->quantity * $orderitem->price; 
            // ou si tu veux prendre le prix depuis l'objet item lié :
            // $total += $orderitem->quantity * $orderitem->item->price;
        }

        // Ajouter le coût de livraison si présent
        if (isset($bill->order->localisation_delivery_price)) {
            $total += $bill->order->localisation_delivery_price;
        }

        // Ajouter l'attribut total_cost à l'objet renvoyé
        $bill->total_cost = $total;

        return response()->json([
            'message' => 'Facture récupérée avec succès.',
            'data' => $bill
        ]);
    }


  
}