<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Bill;
use App\Models\Order;
use App\Models\Payment;

class BillTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Création des dépendances (Order et Payment)
        $this->order = Order::factory()->create(['order_id' => 'ORD001']);
        $this->payment = Payment::factory()->create(['payment_method_id' => 'PMT001']);
        $this->bill = Bill::factory()->create([
            'order_id' => $this->order->order_id,
            'payment_method_id' => $this->payment->payment_method_id,
        ]);
    }

    /** @test */
    public function it_belongs_to_an_order()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->bill->order());
        $this->assertEquals($this->order->order_id, $this->bill->order->order_id);
    }

    /** @test */
    public function it_belongs_to_a_payment_method()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->bill->paymentMethod());
        $this->assertEquals($this->payment->payment_method_id, $this->bill->paymentMethod->payment_method_id);
    }
}