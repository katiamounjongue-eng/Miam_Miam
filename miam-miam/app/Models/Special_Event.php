<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialEvent extends Model
{
    use HasFactory;

    protected $table = 'Special_Event';
    protected $primaryKey = 'event_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; 

    protected $fillable = [
        'event_id',
        'event_type_id',
        'event_name',
        'event_starting_date',
        'event_ending_date',
        'event_description',
    ];

    
    protected $casts = [
        'event_starting_date' => 'date',
        'event_ending_date' => 'date',
    ];
    
    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id', 'event_type_id');
    }
}