<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Payment;
use App\Models\Bill;

class PaymentTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->payment = Payment::factory()->create(['payment_method_id' => 'PMT001']);
    }

    /** @test */
    public function it_uses_the_correct_table_and_primary_key()
    {
        $payment = new Payment();
        $this->assertEquals('Payment', $payment->getTable());
        $this->assertEquals('payment_method_id', $payment->getKeyName());
    }
    
    /** @test */
    public function it_can_have_many_bills()
    {
        // Nécessite la création du modèle Bill pour tester la relation
        // Bill::factory()->create(['payment_method_id' => $this->payment->payment_method_id]);
        
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->payment->bills());
    }
}