<?php

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\StudyPhase;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(ProjectService::class);
    $this->admin = User::factory()->admin()->create();
    $this->engineer = User::factory()->engineer()->create();
});

describe('ProjectService::create', function () {

    it('creates transport project without parent', function () {
        $project = $this->service->create([
            'name' => 'Transport Test',
            'client' => 'Client A',
            'municipality' => 'City',
            'project_type' => ProjectType::Transport->value,
            'study_phase' => StudyPhase::APS->value,
            'gis_project_id' => 'ZNRO-001',
            'created_by' => $this->admin->id,
        ]);

        expect($project)->toBeInstanceOf(Project::class);
        expect($project->project_type)->toBe(ProjectType::Transport);
        expect($project->parent_project_id)->toBeNull();
    });

    it('creates distribution project with transport parent', function () {
        $transport = Project::factory()->transport()->create();

        $project = $this->service->create([
            'name' => 'Distribution Test',
            'client' => 'Client A',
            'municipality' => 'City',
            'project_type' => ProjectType::Distribution->value,
            'study_phase' => StudyPhase::APD->value,
            'gis_project_id' => 'ZNRO-002',
            'parent_project_id' => $transport->id,
            'created_by' => $this->admin->id,
        ]);

        expect($project->project_type)->toBe(ProjectType::Distribution);
        expect($project->parent_project_id)->toBe($transport->id);
    });

    it('throws when transport project has parent', function () {
        $parent = Project::factory()->transport()->create();

        $this->service->create([
            'name' => 'Bad Transport',
            'client' => 'Client A',
            'municipality' => 'City',
            'project_type' => ProjectType::Transport->value,
            'study_phase' => StudyPhase::APS->value,
            'gis_project_id' => 'ZNRO-003',
            'parent_project_id' => $parent->id,
            'created_by' => $this->admin->id,
        ]);
    })->throws(ValidationException::class);

    it('throws when distribution project has no parent', function () {
        $this->service->create([
            'name' => 'Bad Distribution',
            'client' => 'Client A',
            'municipality' => 'City',
            'project_type' => ProjectType::Distribution->value,
            'study_phase' => StudyPhase::APS->value,
            'gis_project_id' => 'ZNRO-004',
            'created_by' => $this->admin->id,
        ]);
    })->throws(ValidationException::class);

    it('throws when distribution parent is not transport', function () {
        $distParent = Project::factory()->distribution()->create();

        $this->service->create([
            'name' => 'Bad Distribution',
            'client' => 'Client A',
            'municipality' => 'City',
            'project_type' => ProjectType::Distribution->value,
            'study_phase' => StudyPhase::APS->value,
            'gis_project_id' => 'ZNRO-005',
            'parent_project_id' => $distParent->id,
            'created_by' => $this->admin->id,
        ]);
    })->throws(ValidationException::class);
});

describe('ProjectService::update', function () {

    it('allows updating project name', function () {
        $project = Project::factory()->create(['name' => 'Old Name']);

        $updated = $this->service->update($project, ['name' => 'New Name']);

        expect($updated->name)->toBe('New Name');
    });

    it('allows forward phase progression', function () {
        $project = Project::factory()->create(['study_phase' => StudyPhase::APS]);

        $updated = $this->service->update($project, ['study_phase' => StudyPhase::APD->value]);

        expect($updated->study_phase)->toBe(StudyPhase::APD);
    });

    it('throws on backward phase progression', function () {
        $project = Project::factory()->create(['study_phase' => StudyPhase::PRO]);

        $this->service->update($project, ['study_phase' => StudyPhase::APS->value]);
    })->throws(ValidationException::class);

    it('throws on status rollback', function () {
        $project = Project::factory()->create(['status' => ProjectStatus::Audited]);

        $this->service->update($project, ['status' => ProjectStatus::Draft->value]);
    })->throws(ValidationException::class);

    it('throws when modifying archived project', function () {
        $project = Project::factory()->create(['status' => ProjectStatus::Archived]);
        $project->delete();

        $this->service->update($project, ['name' => 'Should Fail']);
    })->throws(ValidationException::class);

    it('allows same-phase update', function () {
        $project = Project::factory()->create(['study_phase' => StudyPhase::APD]);

        $updated = $this->service->update($project, ['name' => 'Updated']);

        expect($updated->name)->toBe('Updated');
    });

    it('allows forward status progression', function () {
        $project = Project::factory()->create(['status' => ProjectStatus::Draft]);

        $updated = $this->service->update($project, ['status' => ProjectStatus::InProgress->value]);

        expect($updated->status)->toBe(ProjectStatus::InProgress);
    });
});

describe('ProjectService::list', function () {

    it('returns paginated results for admin', function () {
        Project::factory()->count(5)->create();

        $result = $this->service->list($this->admin);

        expect($result->total())->toBe(5);
    });

    it('filters by search term', function () {
        Project::factory()->create(['name' => 'Fiber Project Alpha']);
        Project::factory()->create(['name' => 'Fiber Project Beta']);
        Project::factory()->create(['name' => 'Different Project']);

        $result = $this->service->list($this->admin, ['search' => 'Alpha']);

        expect($result->total())->toBe(1);
    });

    it('filters by project type', function () {
        Project::factory()->transport()->create();
        Project::factory()->distribution()->create();

        $result = $this->service->list($this->admin, ['project_type' => 'distribution']);

        expect($result->total())->toBe(1);
    });

    it('filters by status', function () {
        Project::factory()->create(['status' => ProjectStatus::Draft]);
        Project::factory()->create(['status' => ProjectStatus::Audited]);

        $result = $this->service->list($this->admin, ['status' => 'draft']);

        expect($result->total())->toBe(1);
    });

    it('sorts by name ascending', function () {
        Project::factory()->create(['name' => 'Zebra']);
        Project::factory()->create(['name' => 'Alpha']);

        $result = $this->service->list($this->admin, ['sort' => 'name', 'direction' => 'asc']);

        expect($result->first()->name)->toBe('Alpha');
    });

    it('archived filter returns only trashed for admin', function () {
        Project::factory()->create(['name' => 'Active']);
        $archived = Project::factory()->create(['name' => 'Archived']);
        $archived->delete();

        $result = $this->service->list($this->admin, ['archived' => true]);

        expect($result->total())->toBe(1);
        expect($result->first()->name)->toBe('Archived');
    });
});

describe('ProjectService::delete and restore', function () {

    it('soft-deletes project', function () {
        $project = Project::factory()->create();

        $this->service->delete($project);

        expect($project->fresh()->trashed())->toBeTrue();
    });

    it('restores soft-deleted project', function () {
        $project = Project::factory()->create();
        $project->delete();

        $this->service->restore($project);

        expect($project->fresh()->trashed())->toBeFalse();
    });
});
