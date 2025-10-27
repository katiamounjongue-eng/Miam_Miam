<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'payment_method_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'payment_method_id',
        'method_name',
        'description', // ✅ CORRIGÉ
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class, 'payment_method_id', 'payment_method_id');
    }
}