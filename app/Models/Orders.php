<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée.
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * Clé primaire de la table.
     *
     * @var string
     */
    protected $primaryKey = 'order_id';

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
        'order_id',
        'user_id',
        'localisation_id',
        'order_statut_id',
        'order_date',
    ];

    /**
     * Les attributs à convertir automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order_date' => 'date',
        ];
    }

    /**
     * Relation : une commande appartient à un utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relation : une commande appartient à un statut de commande.
     */
    public function orderStatut()
    {
        return $this->belongsTo(OrderStatut::class, 'order_statut_id', 'order_statut_id');
    }

    /**
     * Relation : une commande est liée à une localisation.
     */
    public function localisation()
    {
        return $this->belongsTo(Localisation::class, 'localisation_id', 'localisation_id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    /**
     * Relation : la facture de la commande
     */
    public function bill()
    {
        return $this->hasOne(Bill::class, 'order_id', 'order_id');
    }
}
