<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PromoCodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "code" => $this->faker->unique()->name,
            "expiry_date" => $this->faker->date,
            "status" => $this->faker->randomElement(["active", "inactive"]),
            "type" => $this->faker->randomElement(["discount", "percentage"]),
            "discount" => $this->faker->numberBetween(1, 9999),
            "usage_count" => $this->faker->numberBetween(1, 10),
            "usage_count_per_user" => $this->faker->numberBetween(1, 10),
            "usage_type" => $this->faker->randomElement(['private', 'public']),
        ];
    }
}
