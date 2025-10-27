<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $table = 'event_type';

    protected $primaryKey = 'event_type_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'event_type_id',
        'event_type_name',
    ];
    public $timestamps = false;

    
 // les événements spéciaux associés à ce type d'événement.
     
    public function specialEvents()
    {
        return $this->hasMany(SpecialEvent::class, 'event_type_id', 'event_type_id');
    }
}