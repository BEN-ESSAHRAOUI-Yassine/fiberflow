<?php

namespace App\Models;

use App\Enums\AuditStatus;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\StudyPhase;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['name', 'description', 'client', 'municipality', 'project_type', 'study_phase', 'gis_project_id', 'parent_project_id', 'created_by', 'status', 'gis_host', 'gis_port', 'gis_database', 'gis_schema', 'gis_username'])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'project_type' => ProjectType::class,
            'study_phase' => StudyPhase::class,
            'status' => ProjectStatus::class,
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected function getSlugAttribute(): string
    {
        return Str::slug($this->name);
    }

    public function parentProject(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_project_id');
    }

    public function childProjects(): HasMany
    {
        return $this->hasMany(self::class, 'parent_project_id');
    }

    public function datasets(): HasMany
    {
        return $this->hasMany(ProjectDataset::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class);
    }

    public function advanceTo(ProjectStatus $target): bool
    {
        if ($target->order() <= $this->status->order()) {
            return false;
        }

        $this->update(['status' => $target->value]);

        return true;
    }

    public function personalStatus(User $user): ProjectStatus
    {
        $hasAudited = $this->personal_completed_audits
            ?? $this->audits()
                ->where('performed_by', $user->id)
                ->where('status', AuditStatus::Completed)
                ->exists();

        return $hasAudited ? ProjectStatus::Audited : $this->status;
    }
}
