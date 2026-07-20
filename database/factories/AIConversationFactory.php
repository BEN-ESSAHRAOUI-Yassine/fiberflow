<?php

namespace Database\Factories;

use App\Models\Audit;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AIConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'audit_id' => null,
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
        ];
    }

    public function withAudit(): static
    {
        return $this->state(fn () => ['audit_id' => Audit::factory()]);
    }
}
