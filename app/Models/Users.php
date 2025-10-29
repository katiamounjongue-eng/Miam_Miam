<?php

namespace App\Models;
             
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Users extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'user_type_id',
        'first_name',
        'last_name',
        'password',
        'mail_adress',
        'phone_number',
        'inscription_date',
        'account_statut',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'inscription_date' => 'datetime',
        'account_statut' => 'boolean',
    ];

    /**
     * Relation : un utilisateur appartient à un type
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id', 'user_type_id');
    }

    /**
     * Relation : les commandes de l'utilisateur
     */
    public function orders()
    {
        return $this->hasMany(Orders::class, 'user_id', 'user_id');
    }

    /**
     * Relation : les points de fidélité
     */
    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class, 'user_id', 'user_id');
    }

    /**
     * Obtenir le solde total de points
     */
    public function getTotalPointsAttribute()
    {
        return $this->loyaltyPoints()->sum('points');
    }

    /**
     * Override pour Sanctum - utiliser password au lieu de password
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'email' => $this->email,
            'nom' => $this->nom
        ];
    }

}
