<?php

namespace App\Services;

use App\Enums\AuditStatus;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\StudyPhase;
use App\Models\Project;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ProjectService
{
    public function list(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = $user->isAdmin() ? Project::withTrashed() : Project::query();

        $query->withCount([
            'audits as personal_completed_audits' => function ($q) use ($user) {
                $q->where('performed_by', $user->id)
                    ->where('status', AuditStatus::Completed->value);
            },
        ]);

        if ($user->isAdmin() && ($filters['archived'] ?? false)) {
            $query->onlyTrashed();
        }

        if ($search = $filters['search'] ?? null) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('client', 'like', "%{$search}%")
                    ->orWhere('municipality', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($type = $filters['project_type'] ?? null) {
            $query->where('project_type', $type);
        }

        if ($client = $filters['client'] ?? null) {
            $query->where('client', 'like', "%{$client}%");
        }

        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($phase = $filters['study_phase'] ?? null) {
            $query->where('study_phase', $phase);
        }

        $sortField = in_array($filters['sort'] ?? '', ['name', 'client', 'municipality', 'project_type', 'study_phase', 'status', 'created_at'])
            ? $filters['sort']
            : 'created_at';

        $sortDirection = ($filters['direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($sortField, $sortDirection)->paginate()->withQueryString();
    }

    public function create(array $data): Project
    {
        $this->assertValidParentProject($data);

        return Project::create($data);
    }

    public function update(Project $project, array $data): Project
    {
        if ($project->status->value === 'archived') {
            throw ValidationException::withMessages([
                'project' => __('Archived projects cannot be modified.'),
            ]);
        }

        if (isset($data['study_phase'])) {
            $newPhase = $data['study_phase'];
            $currentOrder = $project->study_phase->order();
            $newOrder = StudyPhase::from($newPhase)->order();

            if ($newOrder < $currentOrder) {
                throw ValidationException::withMessages([
                    'study_phase' => __('Study phase cannot move backward.'),
                ]);
            }
        }

        if (isset($data['status'])) {
            $newStatus = $data['status'];
            $currentOrder = $project->status->order();
            $newOrder = ProjectStatus::from($newStatus)->order();

            if ($newOrder < $currentOrder) {
                throw ValidationException::withMessages([
                    'status' => __('Status cannot move backward.'),
                ]);
            }
        }

        $this->assertValidParentProject($data, $project);

        $project->update($data);

        return $project->fresh();
    }

    protected function assertValidParentProject(array $data, ?Project $project = null): void
    {
        $type = $data['project_type'] ?? $project?->project_type->value;
        $parentId = array_key_exists('parent_project_id', $data)
            ? $data['parent_project_id']
            : $project?->parent_project_id;

        if ($type === ProjectType::Transport->value) {
            if ($parentId !== null && $parentId !== '') {
                throw ValidationException::withMessages([
                    'parent_project_id' => __('A transport project cannot have a parent project.'),
                ]);
            }

            return;
        }

        if ($type !== ProjectType::Distribution->value) {
            return;
        }

        if ($parentId === null || $parentId === '') {
            throw ValidationException::withMessages([
                'parent_project_id' => __('A distribution project must have a parent transport project.'),
            ]);
        }

        $parentExists = Project::whereKey($parentId)
            ->where('project_type', ProjectType::Transport->value)
            ->exists();

        if (! $parentExists) {
            throw ValidationException::withMessages([
                'parent_project_id' => __('The parent project must be a transport project.'),
            ]);
        }

        if ($project && (int) $parentId === (int) $project->id) {
            throw ValidationException::withMessages([
                'parent_project_id' => __('A project cannot be its own parent.'),
            ]);
        }
    }

    public function delete(Project $project): void
    {
        $project->delete();
    }

    public function restore(Project $project): void
    {
        $project->restore();
    }
}
