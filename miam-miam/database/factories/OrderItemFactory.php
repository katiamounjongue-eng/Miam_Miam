<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $item = Item::inRandomOrder()->first();

        return [
            // 'order_item_id' sera géré par Trait/Seeder
            'item_id' => $item->item_id ?? null,
            'order_id' => null, // Doit être écrasé dans le Seeder
            'item_quantity' => $this->faker->numberBetween(1, 4),
            'unit_price_at_order' => $item->price ?? $this->faker->randomFloat(2, 500, 5000),
        ];
    }
}
