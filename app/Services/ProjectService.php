<?php

namespace App\Services;

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
        if ($data['project_type'] === ProjectType::Transport->value && isset($data['parent_project_id'])) {
            throw ValidationException::withMessages([
                'parent_project_id' => __('A transport project cannot have a parent project.'),
            ]);
        }

        if ($data['project_type'] === ProjectType::Distribution->value) {
            if (! isset($data['parent_project_id'])) {
                throw ValidationException::withMessages([
                    'parent_project_id' => __('A distribution project must have a parent transport project.'),
                ]);
            }

            $parent = Project::find($data['parent_project_id']);

            if (! $parent || $parent->project_type->value !== ProjectType::Transport->value) {
                throw ValidationException::withMessages([
                    'parent_project_id' => __('The parent project must be a transport project.'),
                ]);
            }
        }

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

        $project->update($data);

        return $project->fresh();
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
