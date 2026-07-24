<?php

namespace App\Models;

use Database\Factories\ProjectDatasetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['project_id', 'geojson', 'imported_at'])]
class ProjectDataset extends Model
{
    /** @use HasFactory<ProjectDatasetFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'geojson' => 'json',
            'imported_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class, 'projectdataset_id');
    }
}
