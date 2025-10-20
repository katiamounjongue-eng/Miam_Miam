<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\EventType;
use App\Models\SpecialEvent; // Pour tester la relation

class EventTypeTest extends TestCase
{
    // Utilise la base de données de test
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Création de données minimales pour les FK
        $this->eventType = EventType::factory()->create(['event_type_id' => 'TST001']);
    }

    /** @test */
    public function it_uses_the_correct_table_and_primary_key()
    {
        $eventType = new EventType();
        $this->assertEquals('Event_Type', $eventType->getTable());
        $this->assertEquals('event_type_id', $eventType->getKeyName());
        $this->assertFalse($eventType->getIncrementing());
        $this->assertEquals('string', $eventType->getKeyType());
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $fillable = ['event_type_id', 'event_type_name'];
        $eventType = new EventType();
        $this->assertEquals($fillable, $eventType->getFillable());
    }

    /** @test */
    public function it_has_many_special_events()
    {
        // Créer un événement spécial lié
        $event = SpecialEvent::factory()->create(['event_type_id' => $this->eventType->event_type_id]);
        
        // Tester que la relation existe et retourne le bon modèle
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->eventType->specialEvents());
        $this->assertTrue($this->eventType->specialEvents->contains($event));
    }
}