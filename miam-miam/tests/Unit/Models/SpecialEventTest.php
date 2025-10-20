<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\SpecialEvent;
use App\Models\EventType;

class SpecialEventTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventType = EventType::factory()->create(['event_type_id' => 'TET001']);
        $this->event = SpecialEvent::factory()->create(['event_type_id' => $this->eventType->event_type_id]);
    }

    /** @test */
    public function it_uses_the_correct_table_and_primary_key()
    {
        $event = new SpecialEvent();
        $this->assertEquals('Special_Event', $event->getTable());
        $this->assertEquals('event_id', $event->getKeyName());
    }

    /** @test */
    public function it_belongs_to_an_event_type()
    {
        // Tester que la relation existe et retourne le bon modèle
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->event->type());
        $this->assertEquals($this->eventType->event_type_id, $this->event->type->event_type_id);
    }
}