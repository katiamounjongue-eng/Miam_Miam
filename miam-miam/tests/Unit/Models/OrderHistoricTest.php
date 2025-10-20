<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\OrderHistoric;
use App\Models\Order;
use App\Models\User;

class OrderHistoricTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_id' => 'USR001']);
        $this->order = Order::factory()->create(['order_id' => 'ORD001', 'user_id' => $this->user->user_id]);
        $this->historic = OrderHistoric::factory()->create([
            'order_id' => $this->order->order_id,
            'user_id' => $this->user->user_id,
        ]);
    }

    /** @test */
    public function it_belongs_to_an_order()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->historic->order());
        $this->assertEquals($this->order->order_id, $this->historic->order->order_id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->historic->user());
        $this->assertEquals($this->user->user_id, $this->historic->user->user_id);
    }
}