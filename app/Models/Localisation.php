<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localisation extends Model
{
    use HasFactory;

    /**
     * Nom de la table associée.
     *
     * @var string
     */
    protected $table = 'localisation';

    /**
     * Clé primaire du modèle.
     *
     * @var string
     */
    protected $primaryKey = 'localisation_id';

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
        'localisation_id',
        'localisation_name',
        'localisation_delevery_price',
    ];

    /**
     * Les attributs à convertir automatiquement.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'localisation_delevery_price' => 'decimal:2',
    ];

    /**
     * Relation : une localisation peut être associée à plusieurs commandes.
     */
    public function orders()
    {
        return $this->hasMany(Orders::class, 'localisation_id', 'localisation_id');
    }
}
