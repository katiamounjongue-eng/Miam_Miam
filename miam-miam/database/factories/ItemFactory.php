<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ItemFactory extends Factory
{
    /**
     * Le nom du modèle correspondant.
     * @var string
     */
    protected $model = Item::class;

    public function definition(): array
    {
        
        $itemName = $this->faker->unique()->words(rand(2, 4), true);
        
        return [
            
            'item_type_id' => null, 
            
            'name' => Str::title($itemName),
            'description' => $this->faker->sentence(),
            'quantity' => $this->faker->numberBetween(10, 100), // Stock initial
            'price' => $this->faker->randomFloat(2, 500, 5000), // Prix entre 500 et 5000
            'image_link' => $this->faker->imageUrl(640, 480, 'food', true),
        ];
    }
}
