<?php

namespace App\Models;

use App\Enums\AuditStatus;
use App\Enums\ProjectType;
use App\Enums\StudyPhase;
use Database\Factories\AuditFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'projectdataset_id',
    'performed_by',
    'project_type_at_audit',
    'phase_at_audit',
    'status',
    'quality_score',
    'connectivity_score',
    'coherence_score',
    'capacity_score',
    'extensibility_score',
    'network_statistics',
    'ai_summary',
    'recommendations',
    'anomaly_count',
    'critical_anomaly_count',
    'model_used',
    'tokens_used',
    'error_message',
    'started_at',
    'completed_at',
])]
class Audit extends Model
{
    /** @use HasFactory<AuditFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'project_type_at_audit' => ProjectType::class,
            'phase_at_audit' => StudyPhase::class,
            'status' => AuditStatus::class,
            'quality_score' => 'decimal:2',
            'connectivity_score' => 'decimal:2',
            'coherence_score' => 'decimal:2',
            'capacity_score' => 'decimal:2',
            'extensibility_score' => 'decimal:2',
            'network_statistics' => 'json',
            'recommendations' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getAiSummaryAttribute($value): array|string|null
    {
        if ($value === null) {
            return null;
        }
        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : $value;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(ProjectDataset::class, 'projectdataset_id');
    }

    public function isRetryable(): bool
    {
        if ($this->status === AuditStatus::Failed) {
            return true;
        }

        return $this->status === AuditStatus::Running
            && $this->updated_at !== null
            && $this->updated_at->lt(now()->subMinutes(30));
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function weightedScore(): ?float
    {
        if ($this->quality_score !== null) {
            return (float) $this->quality_score;
        }

        $scores = [
            $this->connectivity_score,
            $this->coherence_score,
            $this->capacity_score,
            $this->extensibility_score,
        ];

        if (collect($scores)->contains(null)) {
            return null;
        }

        return round(
            $this->connectivity_score * 0.40 +
            $this->coherence_score * 0.30 +
            $this->capacity_score * 0.20 +
            $this->extensibility_score * 0.10,
            2
        );
    }
}
