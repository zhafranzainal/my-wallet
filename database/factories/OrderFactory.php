<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'product_id' => Product::inRandomOrder()->pluck('id')->first(),
            'quantity' => $this->faker->randomNumber(3, false),
            'name' => $this->faker->name(),
            'address' => $this->faker->address,
        ];
    }
}
