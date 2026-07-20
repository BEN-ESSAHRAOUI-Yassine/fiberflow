<?php

namespace App\Models;

use App\Enums\AuditStatus;
use App\Enums\ProjectType;
use App\Enums\StudyPhase;
use Database\Factories\AuditFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(ProjectDataset::class, 'projectdataset_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AIConversation::class);
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
