<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    
    public function run(): void
    {
       
        DB::table('Payment')->insert([
            [
                'payment_method_id' => 'PAY001',
                'method_name' => 'Mobile Money (Orange/Mtn)'
            ],
            [
                'payment_method_id' => 'PAY002',
                'method_name' => 'Carte Bancaire'
            ],
            [
                'payment_method_id' => 'PAY003',
                'method_name' => 'Points de Fidélité'
            ],
        ]);
    }
}