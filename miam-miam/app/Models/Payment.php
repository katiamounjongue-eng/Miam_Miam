<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'Payment';
    protected $primaryKey = 'payment_method_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'payment_method_id',
        'method_name',
        'payment_date', 
    ];


    protected $casts = [
        'payment_date' => 'date',
    ];
    
    // Obtenir les factures  utilisant cette méthode de paiement.
    
    public function bills()
    {
        
        return $this->hasMany(Bill::class, 'payment_method_id', 'payment_method_id');
    }
}