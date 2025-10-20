<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'Bill';
    protected $primaryKey = 'bill_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; 
    protected $fillable = [
        'bill_id',
        'order_id',
        'total_cost',
        'payment_method_id',
    ];

    
    //Obtenir la commande associée à cette facture.
     
    public function order()
    {
        // Supposons que Order est le nom du modèle pour la table Orders
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    
   //  la méthode de paiement utilisée pour cette facture.
    
    public function paymentMethod()
    {
        return $this->belongsTo(Payment::class, 'payment_method_id', 'payment_method_id');
    }
}