<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\StudyPhase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'parent_project_id' => null,
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'client' => fake()->company(),
            'municipality' => fake()->city(),
            'project_type' => ProjectType::Transport,
            'study_phase' => StudyPhase::APS,
            'gis_project_id' => fake()->bothify('ZNRO-####'),
            'status' => ProjectStatus::Draft,
        ];
    }

    public function transport(): static
    {
        return $this->state(fn () => ['project_type' => ProjectType::Transport, 'parent_project_id' => null]);
    }

    public function distribution(?Factory $parent = null): static
    {
        return $this->state(fn () => ['project_type' => ProjectType::Distribution, 'parent_project_id' => $parent ?? ProjectFactory::new()->transport()]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => ProjectStatus::Draft]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => ProjectStatus::InProgress]);
    }

    public function audited(): static
    {
        return $this->state(fn () => ['status' => ProjectStatus::Audited]);
    }

    public function validated(): static
    {
        return $this->state(fn () => ['status' => ProjectStatus::Validated]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => ProjectStatus::Archived]);
    }
}
