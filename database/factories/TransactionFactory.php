<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'id' => $this->faker->randomNumber(2, false),
            'user_id' => User::inRandomOrder()->pluck('id')->first(),
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'status' => $this->faker->randomElement(['Pending' ,'Failed', 'Success']),
        ];
    }
}
