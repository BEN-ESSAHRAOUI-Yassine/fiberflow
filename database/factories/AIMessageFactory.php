<?php

namespace Database\Factories;

use App\Enums\MessageRole;
use App\Models\AIConversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AIMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => AIConversation::factory(),
            'role' => MessageRole::User,
            'content' => fake()->paragraph(),
        ];
    }

    public function userMessage(): static
    {
        return $this->state(fn () => ['role' => MessageRole::User]);
    }

    public function assistantMessage(): static
    {
        return $this->state(fn () => ['role' => MessageRole::Assistant]);
    }
}
