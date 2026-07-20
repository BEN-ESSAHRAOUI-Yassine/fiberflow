<?php

namespace Database\Factories;

use App\Enums\AuditStatus;
use App\Enums\ProjectType;
use App\Enums\StudyPhase;
use App\Models\Project;
use App\Models\ProjectDataset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'projectdataset_id' => ProjectDataset::factory(),
            'performed_by' => User::factory(),
            'project_type_at_audit' => ProjectType::Transport,
            'phase_at_audit' => StudyPhase::PRO,
            'status' => AuditStatus::Pending,
            'quality_score' => null,
            'connectivity_score' => null,
            'coherence_score' => null,
            'capacity_score' => null,
            'extensibility_score' => null,
            'network_statistics' => null,
            'ai_summary' => null,
            'recommendations' => null,
            'anomaly_count' => 0,
            'critical_anomaly_count' => 0,
            'model_used' => null,
            'tokens_used' => null,
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => AuditStatus::Completed,
            'quality_score' => fake()->randomFloat(2, 0, 100),
            'connectivity_score' => fake()->randomFloat(2, 0, 100),
            'coherence_score' => fake()->randomFloat(2, 0, 100),
            'capacity_score' => fake()->randomFloat(2, 0, 100),
            'extensibility_score' => fake()->randomFloat(2, 0, 100),
            'started_at' => now()->subHour(),
            'completed_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => AuditStatus::Failed,
            'error_message' => fake()->sentence(),
        ]);
    }
}
