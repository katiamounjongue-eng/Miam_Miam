<?php

namespace App\Models;
             
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Users extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

   
/**
     * Table associate.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * primary key.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Auto-incrementing database.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Primary key type.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'user_type_id',
        'first_name',
        'last_name',
        'user_password',
        'mail_adress',
        'phone_number',
        'inscription_date',
        'account_statut',
    ];

    /**
     * Attributs.
     *
     * @var list<string>
     */
    protected $hidden = [
        'user_password',
        'remember_token',
    ];

    /**
     * Les attributs à convertir automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'inscription_date' => 'datetime',
            'account_statut' => 'boolean',
        ];
    }

    /**
     * Relation : un utilisateur appartient à un type d'utilisateur.
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id', 'user_type_id');
    }
}
