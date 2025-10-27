<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bill';
    protected $primaryKey = 'bill_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'bill_id',
        'order_id',
        'total_cost',
        'payment_method_id',
        'payment_date', // ✅ C'est ICI que payment_date doit être, pas dans Payment
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Relation : la commande associée à cette facture
     */
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }

    /**
     * Relation : la méthode de paiement utilisée
     */
    public function paymentMethod()
    {
        return $this->belongsTo(Payment::class, 'payment_method_id', 'payment_method_id');
    }

    /**
     * Vérifier si la facture est payée
     */
    public function isPaid(): bool
    {
        return $this->payment_date !== null;
    }
}