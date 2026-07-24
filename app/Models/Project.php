<?php

namespace App\Models;

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

#[Fillable(['name', 'description', 'client', 'municipality', 'project_type', 'study_phase', 'gis_project_id', 'parent_project_id', 'created_by', 'status'])]
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

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AIConversation::class);
    }
}
