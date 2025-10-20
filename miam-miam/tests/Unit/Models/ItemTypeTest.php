<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ItemType;
use App\Models\Item;

class ItemTypeTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->itemType = ItemType::factory()->create(['item_type_id' => 'TIT001']);
    }

    /** @test */
    public function it_uses_the_correct_table_and_primary_key()
    {
        $itemType = new ItemType();
        $this->assertEquals('Item_type', $itemType->getTable());
        $this->assertEquals('item_type_id', $itemType->getKeyName());
    }

    /** @test */
    public function it_has_many_items()
    {
        // Créer un article lié
        $item = Item::factory()->create(['item_type_id' => $this->itemType->item_type_id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->itemType->items());
        $this->assertTrue($this->itemType->items->contains($item));
    }
}