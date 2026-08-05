<?php

use App\Enums\ProjectStatus;
use App\Enums\StudyPhase;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->engineer = User::factory()->engineer()->create();
});

describe('GET /api/v1/projects', function () {

    it('returns paginated projects for admin', function () {
        Project::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/projects');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('returns projects for engineer (without archived)', function () {
        Project::factory()->count(2)->create(['status' => ProjectStatus::Draft]);
        $archived = Project::factory()->create(['status' => ProjectStatus::Archived]);
        $archived->delete();

        $response = $this->actingAs($this->engineer)
            ->getJson('/api/v1/projects');

        $response->assertOk();
        expect(count($response->json('data')))->toBe(2);
    });

    it('returns 401 for guest', function () {
        $response = $this->getJson('/api/v1/projects');

        $response->assertUnauthorized();
    });
});

describe('POST /api/v1/projects', function () {

    it('creates transport project for admin', function () {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/projects', [
                'name' => 'Test Transport',
                'client' => 'Client A',
                'municipality' => 'City',
                'project_type' => 'transport',
                'study_phase' => 'APS',
                'gis_project_id' => 'ZNRO-001',
            ]);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'project_type']]);
        expect($response->json('data.project_type'))->toBe('transport');
    });

    it('creates distribution project with transport parent for admin', function () {
        $transport = Project::factory()->transport()->create();

        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/projects', [
                'name' => 'Test Distribution',
                'client' => 'Client A',
                'municipality' => 'City',
                'project_type' => 'distribution',
                'study_phase' => 'APD',
                'gis_project_id' => 'ZNRO-002',
                'parent_project_id' => $transport->id,
            ]);

        $response->assertCreated();
        expect($response->json('data.project_type'))->toBe('distribution');
    });

    it('rejects distribution without parent project', function () {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/projects', [
                'name' => 'Bad Distribution',
                'client' => 'Client A',
                'municipality' => 'City',
                'project_type' => 'distribution',
                'study_phase' => 'APS',
                'gis_project_id' => 'ZNRO-003',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('parent_project_id');
    });

    it('rejects transport with parent project', function () {
        $parent = Project::factory()->transport()->create();

        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/projects', [
                'name' => 'Bad Transport',
                'client' => 'Client A',
                'municipality' => 'City',
                'project_type' => 'transport',
                'study_phase' => 'APS',
                'gis_project_id' => 'ZNRO-004',
                'parent_project_id' => $parent->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('parent_project_id');
    });

    it('rejects distribution with non-transport parent', function () {
        $dist = Project::factory()->distribution()->create();

        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/projects', [
                'name' => 'Bad Distribution',
                'client' => 'Client A',
                'municipality' => 'City',
                'project_type' => 'distribution',
                'study_phase' => 'APS',
                'gis_project_id' => 'ZNRO-005',
                'parent_project_id' => $dist->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('parent_project_id');
    });

    it('returns 403 for engineer', function () {
        $response = $this->actingAs($this->engineer)
            ->postJson('/api/v1/projects', [
                'name' => 'Test',
                'client' => 'Client A',
                'municipality' => 'City',
                'project_type' => 'transport',
                'study_phase' => 'APS',
                'gis_project_id' => 'ZNRO-006',
            ]);

        $response->assertForbidden();
    });
});

describe('GET /api/v1/projects/{id}', function () {

    it('shows project for admin', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/projects/{$project->id}");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'project_type']]);
    });

    it('shows own project for engineer', function () {
        $project = Project::factory()->create(['created_by' => $this->engineer->id]);

        $response = $this->actingAs($this->engineer)
            ->getJson("/api/v1/projects/{$project->id}");

        $response->assertOk();
    });

    it('allows engineer to view any project (view policy is open)', function () {
        $project = Project::factory()->create(['created_by' => $this->admin->id]);

        $response = $this->actingAs($this->engineer)
            ->getJson("/api/v1/projects/{$project->id}");

        $response->assertOk();
    });
});

describe('PUT /api/v1/projects/{id}', function () {

    it('updates project for admin', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$project->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertOk();
        expect($project->fresh()->name)->toBe('Updated Name');
    });

    it('rejects project type change', function () {
        $project = Project::factory()->transport()->create();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$project->id}", [
                'project_type' => 'distribution',
            ]);

        $response->assertOk();
        expect($project->fresh()->project_type->value)->toBe('transport');
    });

    it('rejects backward phase progression', function () {
        $project = Project::factory()->create(['study_phase' => StudyPhase::PRO]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$project->id}", [
                'study_phase' => 'APS',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('study_phase');
    });

    it('allows forward phase progression', function () {
        $project = Project::factory()->create(['study_phase' => StudyPhase::APS]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$project->id}", [
                'study_phase' => 'APD',
            ]);

        $response->assertOk();
        expect($project->fresh()->study_phase->value)->toBe('APD');
    });

    it('rejects status rollback', function () {
        $project = Project::factory()->create(['status' => ProjectStatus::Audited]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$project->id}", [
                'status' => 'draft',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('status');
    });

    it('rejects modification of archived project', function () {
        $project = Project::factory()->create(['status' => ProjectStatus::Archived]);
        $project->delete();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$project->id}", [
                'name' => 'Should Fail',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('project');
    });

    it('rejects giving a transport project a parent on update', function () {
        $transport = Project::factory()->transport()->create();
        $parent = Project::factory()->transport()->create();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$transport->id}", [
                'parent_project_id' => $parent->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('parent_project_id');
        expect($transport->fresh()->parent_project_id)->toBeNull();
    });

    it('rejects removing the parent from a distribution project on update', function () {
        $transport = Project::factory()->transport()->create();
        $distribution = Project::factory()->distribution()->create(['parent_project_id' => $transport->id]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$distribution->id}", [
                'parent_project_id' => null,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('parent_project_id');
    });

    it('rejects reparenting a distribution project to a non-transport project', function () {
        $transport = Project::factory()->transport()->create();
        $distribution = Project::factory()->distribution()->create(['parent_project_id' => $transport->id]);
        $otherDistribution = Project::factory()->distribution()->create(['parent_project_id' => $transport->id]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$distribution->id}", [
                'parent_project_id' => $otherDistribution->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('parent_project_id');
    });

    it('rejects self-reference as parent on update', function () {
        $transport = Project::factory()->transport()->create();
        $distribution = Project::factory()->distribution()->create(['parent_project_id' => $transport->id]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$distribution->id}", [
                'parent_project_id' => $distribution->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('parent_project_id');
    });

    it('allows reparenting a distribution project to another transport project', function () {
        $oldTransport = Project::factory()->transport()->create();
        $newTransport = Project::factory()->transport()->create();
        $distribution = Project::factory()->distribution()->create(['parent_project_id' => $oldTransport->id]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$distribution->id}", [
                'parent_project_id' => $newTransport->id,
            ]);

        $response->assertOk();
        expect((int) $distribution->fresh()->parent_project_id)->toBe($newTransport->id);
    });

    it('returns 403 for engineer', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->engineer)
            ->putJson("/api/v1/projects/{$project->id}", [
                'name' => 'Should Fail',
            ]);

        $response->assertForbidden();
    });
});

describe('DELETE /api/v1/projects/{id}', function () {

    it('soft-deletes project for admin', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/v1/projects/{$project->id}");

        $response->assertNoContent();
        expect($project->fresh()->trashed())->toBeTrue();
    });

    it('returns 403 for engineer', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->engineer)
            ->deleteJson("/api/v1/projects/{$project->id}");

        $response->assertForbidden();
    });
});

describe('PUT /api/v1/projects/{id}/restore', function () {

    it('restores project for admin', function () {
        $project = Project::factory()->create();
        $project->delete();

        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/projects/{$project->id}/restore");

        $response->assertOk();
        expect($project->fresh()->trashed())->toBeFalse();
    });

    it('returns 403 for engineer', function () {
        $project = Project::factory()->create();
        $project->delete();

        $response = $this->actingAs($this->engineer)
            ->putJson("/api/v1/projects/{$project->id}/restore");

        $response->assertForbidden();
    });
});

describe('StoreProjectRequest validation', function () {

    it('requires name', function () {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/projects', [
                'client' => 'Client',
                'municipality' => 'City',
                'project_type' => 'transport',
                'study_phase' => 'APS',
                'gis_project_id' => 'ZNRO-001',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('name');
    });

    it('rejects invalid project type', function () {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/projects', [
                'name' => 'Test',
                'client' => 'Client',
                'municipality' => 'City',
                'project_type' => 'invalid',
                'study_phase' => 'APS',
                'gis_project_id' => 'ZNRO-001',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('project_type');
    });

    it('rejects invalid study phase', function () {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/projects', [
                'name' => 'Test',
                'client' => 'Client',
                'municipality' => 'City',
                'project_type' => 'transport',
                'study_phase' => 'INVALID',
                'gis_project_id' => 'ZNRO-001',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('study_phase');
    });

    it('rejects name exceeding max length', function () {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/projects', [
                'name' => str_repeat('a', 151),
                'client' => 'Client',
                'municipality' => 'City',
                'project_type' => 'transport',
                'study_phase' => 'APS',
                'gis_project_id' => 'ZNRO-001',
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors('name');
    });
});

describe('Archive behavior', function () {

    it('preserves data after soft delete', function () {
        $project = Project::factory()->create(['name' => 'Archived Project']);
        $projectId = $project->id;
        $project->delete();

        $this->assertDatabaseHas('projects', [
            'id' => $projectId,
            'name' => 'Archived Project',
        ]);
        expect(Project::withTrashed()->find($projectId))->not->toBeNull();
    });

    it('excludes archived projects from normal query', function () {
        Project::factory()->create(['name' => 'Active']);
        $archived = Project::factory()->create(['name' => 'Archived']);
        $archived->delete();

        $projects = Project::pluck('name');
        expect($projects)->toContain('Active');
        expect($projects)->not->toContain('Archived');
    });

    it('includes archived projects in withTrashed query', function () {
        $archived = Project::factory()->create(['name' => 'Archived']);
        $archived->delete();

        $projects = Project::withTrashed()->pluck('name');
        expect($projects)->toContain('Archived');
    });

    it('restore brings project back to active', function () {
        $project = Project::factory()->create();
        $project->delete();
        expect($project->fresh()->trashed())->toBeTrue();

        $project->restore();
        expect($project->fresh()->trashed())->toBeFalse();
    });
});

describe('UserFactory states', function () {

    it('creates admin user with admin role', function () {
        $admin = User::factory()->admin()->create();

        expect($admin->role->value)->toBe('admin');
        expect($admin->isAdmin())->toBeTrue();
    });

    it('creates engineer user with ingenieur role', function () {
        $engineer = User::factory()->engineer()->create();

        expect($engineer->role->value)->toBe('ingenieur');
        expect($engineer->isAdmin())->toBeFalse();
    });
});
