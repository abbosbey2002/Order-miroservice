<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    /**
     * Defining the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_email' => $this->faker->email(), // Yangi foydalanuvchi emaili
            'product_name' => $this->faker->word(), // Tasodifiy mahsulot nomi
            'price' => $this->faker->numberBetween(100, 1000), // Tasodifiy narx
            'status' => $this->faker->randomElement(['pending', 'processed', 'shipped']), // Tasodifiy status
        ];
    }

    /**
     * Define a state for a specific user.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forUser(User $user): Factory
    {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_email' => $user->email, // Buyurtma foydalanuvchi emailiga bog'lanadi
            ];
        });
    }
}