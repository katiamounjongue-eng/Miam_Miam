<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderitem extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée.
     *
     * @var string
     */
    protected $table = 'order_item';

    /**
     * Clé primaire du modèle.
     *
     * @var string
     */
    protected $primaryKey = 'order_item_id';

    /**
     * Indique si la clé primaire est auto-incrémentée.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Type de la clé primaire.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Les attributs pouvant être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_item_id',
        'item_id',
        'order_id',
        'item_quantity',
    ];

    /**
     * Relation : un élément de commande appartient à une commande.
     */
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }

    /**
     * Relation : un élément de commande correspond à un produit (item).
     */
    public function item()
    {
        return $this->belongsTo(item::class, 'item_id', 'item_id');
    }
}
