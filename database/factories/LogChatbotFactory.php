<?php

namespace Database\Factories;

use App\Models\LogChatbot;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LogChatbot> */
class LogChatbotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => null,
            'produk_id' => null,
            'pertanyaan' => 'stok '.fake()->word(),
            'jawaban' => fake()->sentence(),
        ];
    }
}
