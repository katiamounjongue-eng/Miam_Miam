<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Type extends Model
{
use HasFactory;

    protected $table = 'user_type';
    protected $primaryKey = 'user_type_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'user_type_id',
        'user_type_name',
    ];

    /**
     * Relation : un type d'utilisateur a plusieurs utilisateurs
     */
    public function users()
    {
        return $this->hasMany(Users::class, 'user_type_id', 'user_type_id');
    }
}
