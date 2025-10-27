<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class itemType extends Model
{
    use HasFactory;

    protected $table = 'item_type';
    protected $primaryKey = 'item_type_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'item_type_id',
        'item_type_name',
    ];

    
//  les articles  associés à ce type.

    public function items()
    {
        return $this->hasMany(item::class, 'item_type_id', 'item_type_id');
    }
}