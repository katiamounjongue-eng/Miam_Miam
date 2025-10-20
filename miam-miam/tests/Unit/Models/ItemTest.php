<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Item;
use App\Models\ItemType;
use App\Models\OrderItem;

class ItemTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->itemType = ItemType::factory()->create(['item_type_id' => 'TIT001']);
        $this->item = Item::factory()->create(['item_type_id' => $this->itemType->item_type_id, 'item_id' => 'ITM001']);
    }

    /** @test */
    public function it_belongs_to_an_item_type()
    {
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $this->item->type());
        $this->assertEquals($this->itemType->item_type_id, $this->item->type->item_type_id);
    }

    /** @test */
    public function it_has_many_order_items()
    {
        // Créer un détail de commande lié
        // Nécessite la création des dépendances pour OrderItem (Order, etc.)
        // OrderItem::factory()->create(['item_id' => $this->item->item_id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $this->item->orderItems());
    }
}