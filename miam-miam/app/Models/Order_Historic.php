<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistoric extends Model
{
    use HasFactory;

    protected $table = 'Order_Historic';
    protected $primaryKey = 'historic_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'historic_id',
        'order_id',
        'user_id',
    ];

    
    // Obtenir la commande associée.
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    
     //Obtenir l'utilisateur associé.
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}