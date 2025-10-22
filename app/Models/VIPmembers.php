<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class VIP_members extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'VIP_members';
    protected $primaryKey = 'vip_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'vip_starting_date',
        'vip_ending_date',
    ];

    protected $casts = [
        'vip_starting_date' => 'datetime',
        'vip_ending_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'user_id');
    }
}
