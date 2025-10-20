<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    
    public function index()
    {
       
        $paymentMethods = Payment::all();
        
        return response()->json($paymentMethods);
    }

    
     
    public function show($id)
    {
        $paymentMethod = Payment::findOrFail($id);
        return response()->json($paymentMethod);
    }
    
}