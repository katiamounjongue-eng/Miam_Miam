<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistoric extends Model
{
    use HasFactory;

    protected $table = 'order_historic';
    protected $primaryKey = 'historic_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'historic_id',
        'order_id',
        'user_id',
    ];

    /**
     * Relation : la commande associée
     */
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }

    /**
     * Relation : l'utilisateur associé
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}