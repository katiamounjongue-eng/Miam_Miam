<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class item extends Model
{
    use HasFactory;

    protected $table = 'item';
    protected $primaryKey = 'item_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'item_type_id',
        'name',
        'description',
        'quantity',
        'price',
        'image_link',
    ];

    
    // le type d'article.
    
    public function type()
    {
        return $this->belongsTo(itemType::class, 'item_type_id', 'item_type_id');
    }

    
    // les détails de commande liés à cet article .
    
    public function orderitems()
    {
        // Supposons que Orderitem est le modèle pour la table order_item
        return $this->hasMany(Orderitem::class, 'item_id', 'item_id');
    }
}