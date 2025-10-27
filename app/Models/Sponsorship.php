<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponsorship extends Model
{
    use HasFactory;

    protected $table = 'sponsorships';
    protected $primaryKey = 'sponsorship_relation_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'sponsorship_relation_id',
        'student_id',
        'godchild_id',
        // ✅ CORRIGÉ : sponsorship_code au lieu de sponsordhip_code
        'sponsorship_code',
    ];

    /**
     * Relation : le parrain (sponsor)
     */
    public function sponsor()
    {
        return $this->belongsTo(Users::class, 'student_id', 'user_id');
    }

    /**
     * Relation : le filleul (godchild)
     */
    public function godchild()
    {
        return $this->belongsTo(Users::class, 'godchild_id', 'user_id');
    }

    /**
     * Obtenir tous les filleuls d'un parrain
     */
    public function godchildren()
    {
        return $this->hasMany(Sponsorship::class, 'student_id', 'student_id')
            ->whereNotNull('godchild_id');
    }
}