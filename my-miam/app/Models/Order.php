<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'total_amount',
        'discount_used',
        'status',
        'type',
        'delivery_address',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_used' => 'decimal:2',
    ];

    // Une commande a plusieurs articles
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Une commande appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Une commande appartient à un restaurant
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
