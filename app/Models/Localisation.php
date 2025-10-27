<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Localisation extends Model
{
    use HasFactory;

    protected $table = 'localisation';
    protected $primaryKey = 'localisation_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'localisation_id',
        'localisation_name',
        // ✅ CORRIGÉ : delivery au lieu de delevery
        'localisation_delivery_price',
    ];

    protected $casts = [
        'localisation_delivery_price' => 'decimal:2',
    ];

    /**
     * Relation : une localisation peut être associée à plusieurs commandes
     */
    public function orders()
    {
        return $this->hasMany(Orders::class, 'localisation_id', 'localisation_id');
    }
}
