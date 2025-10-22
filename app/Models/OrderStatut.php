<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatut extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée.
     *
     * @var string
     */
    protected $table = 'order_statut';

    /**
     * Clé primaire du modèle.
     *
     * @var string
     */
    protected $primaryKey = 'order_statut_id';

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
        'order_statut_id',
        'order_statut_name',
    ];

    /**
     * Relation : un statut peut être associé à plusieurs commandes.
     */
    public function orders()
    {
        return $this->hasMany(Orders::class, 'order_statut_id', 'order_statut_id');
    }
}

