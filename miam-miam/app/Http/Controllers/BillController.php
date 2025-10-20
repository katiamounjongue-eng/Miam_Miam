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
        // Récupération complète de la facture avec les détails de la commande associée
        $bill = Bill::with(['order.user', 'paymentMethod', 'order.items'])
                    ->findOrFail($id);

        return response()->json($bill);
    }

  
}