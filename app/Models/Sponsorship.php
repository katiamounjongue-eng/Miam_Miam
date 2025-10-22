<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

   
/**
     * Table associate.
     *
     * @var string
     */
    protected $table = 'Sponsorship';

    /**
     * primary key.
     *
     * @var string
     */
    protected $primaryKey = 'sponsorship_relation_id';

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
        'sponsorship_relation_id',
        'student_id',
        'godchild_id',
        'sponsordhip_code',
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
        'sponsordhip_code '    ];

    /**
     * Les attributs à convertir automatiquement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {   }

    /**
     * Relation : un utilisateur appartient à un type d'utilisateur.
     */
    public function userType()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}
