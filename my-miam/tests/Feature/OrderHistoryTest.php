<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        // Créer un utilisateur employé (doit avoir accès à la route)
        $this->employee = User::factory()->create();
        $this->employee->assignRole('employee');

        // Créer des commandes de test (avec des statuts différents)
        Order::factory()->count(5)->create(['status' => 'completed']);
        Order::factory()->count(2)->create(['status' => 'rejected']);
        Order::factory()->create([
            'status' => 'pending',
            'created_at' => now()->subDays(5),
        ]);
        Order::factory()->create([
            'status' => 'pending',
            'created_at' => now()->subDays(10),
        ]);
    }

    /** @test */
    public function it_filters_by_status_and_sorts_correctly()
    {
        $response = $this->actingAs($this->employee, 'api')
                         ->getJson('/api/orders/history?status=completed&sort_by=total_amount&sort_order=asc');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function it_filters_by_date_range()
    {
        $startDate = now()->subDays(6)->toDateString();
        $endDate = now()->toDateString();

        $response = $this->actingAs($this->employee, 'api')
                         ->getJson("/api/orders/history?start_date={$startDate}&end_date={$endDate}");

        $response->assertStatus(200)
                 ->assertJsonCount(8, 'data');
    }

    /** @test */
    public function it_rejects_invalid_sort_parameter()
    {
        $response = $this->actingAs($this->employee, 'api')
                         ->getJson('/api/orders/history?sort_by=password');

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['sort_by']);
    }
}
