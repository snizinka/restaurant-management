<?php

namespace Database\Factories;

use App\Models\DishCategory;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dish>
 */
class DishFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => DishCategory::all()->random()->id,
            'restaurant_id' => Restaurant::all()->random()->id,
            'name' => $this->faker->unique()->sentence(),
            'ingredients' => $this->faker->text(),
            'price' => $this->faker->text()
        ];
    }
}
