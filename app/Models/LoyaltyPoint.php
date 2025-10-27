<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $table = 'loyalty_points';
    protected $primaryKey = 'loyalty_point_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'loyalty_point_id',
        'user_id',
        'points',
        'transaction_date',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'points' => 'integer',
    ];

    /**
     * Relation : appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}
