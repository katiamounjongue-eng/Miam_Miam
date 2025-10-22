<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Type extends Model
{
/** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

   
/**
     * Table associate.
     *
     * @var string
     */
    protected $table = 'User_Type';

    /**
     * primary key.
     *
     * @var string
     */
    protected $primaryKey = 'user_type_id';

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
        'user_type_id ',
        'user_type_name',
    ];

    /**
     * Attributs.
     *
     * @var list<string>
     */
    protected $hidden = [

    ];

    /**
     * Les attributs à convertir automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    { }

    /**
     * Relation : un utilisateur appartient à un type d'utilisateur.
     */
    public function userType(){}
    
}
