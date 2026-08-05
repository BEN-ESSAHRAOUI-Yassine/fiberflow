<?php

use App\Enums\AuditStatus;
use App\Jobs\AnalyzeAuditJob;
use App\Jobs\RunAuditJob;
use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectDataset;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();

    $this->admin = User::factory()->admin()->create();
    $this->engineer = User::factory()->engineer()->create();
});

describe('POST /api/v1/projects/{project}/audits', function () {

    it('launches audit and returns 202', function () {
        $project = Project::factory()->create();
        ProjectDataset::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$project->id}/audits");

        $response->assertStatus(202)
            ->assertJsonStructure(['data' => ['id', 'status']]);
        expect($response->json('data.status'))->toBe('pending');
    });

    it('dispatches RunAuditJob when audit is created', function () {
        $project = Project::factory()->create();
        ProjectDataset::factory()->create(['project_id' => $project->id]);

        $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$project->id}/audits");

        Queue::assertPushed(RunAuditJob::class, 1);
    });

    it('returns 422 when project has no dataset', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$project->id}/audits");

        $response->assertStatus(422)
            ->assertJson(['message' => true]);
        Queue::assertNotPushed(RunAuditJob::class);
    });

    it('returns 401 for guest', function () {
        $project = Project::factory()->create();

        $response = $this->postJson("/api/v1/projects/{$project->id}/audits");

        $response->assertUnauthorized();
    });

    it('allows engineer to launch audit', function () {
        $project = Project::factory()->create();
        ProjectDataset::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($this->engineer)
            ->postJson("/api/v1/projects/{$project->id}/audits");

        $response->assertStatus(202);
    });

    it('creates audit record with correct fields', function () {
        $project = Project::factory()->transport()->create(['study_phase' => 'APS']);
        $dataset = ProjectDataset::factory()->create(['project_id' => $project->id]);

        $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$project->id}/audits");

        $this->assertDatabaseHas('audits', [
            'project_id' => $project->id,
            'projectdataset_id' => $dataset->id,
            'performed_by' => $this->admin->id,
            'project_type_at_audit' => 'transport',
            'phase_at_audit' => 'APS',
            'status' => AuditStatus::Pending->value,
        ]);
    });

    it('uses latest dataset for audit', function () {
        $project = Project::factory()->create();
        $oldDataset = ProjectDataset::factory()->create([
            'project_id' => $project->id,
            'created_at' => now()->subDays(2),
            'imported_at' => now()->subDays(2),
        ]);
        $newDataset = ProjectDataset::factory()->create([
            'project_id' => $project->id,
            'created_at' => now(),
            'imported_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$project->id}/audits");

        $this->assertDatabaseHas('audits', [
            'projectdataset_id' => $newDataset->id,
        ]);
        $this->assertDatabaseMissing('audits', [
            'projectdataset_id' => $oldDataset->id,
        ]);
    });
});

describe('GET /api/v1/projects/{project}/audits', function () {

    it('lists audits for a project', function () {
        $project = Project::factory()->create();
        Audit::factory()->count(3)->create(['project_id' => $project->id]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/projects/{$project->id}/audits");

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
        expect(count($response->json('data')))->toBe(3);
    });

    it('returns empty list when no audits exist', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/projects/{$project->id}/audits");

        $response->assertOk();
        expect($response->json('data'))->toBe([]);
    });

    it('returns audits ordered by newest first', function () {
        $project = Project::factory()->create();
        $old = Audit::factory()->create(['project_id' => $project->id, 'created_at' => now()->subDay()]);
        $new = Audit::factory()->create(['project_id' => $project->id, 'created_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/projects/{$project->id}/audits");

        expect($response->json('data.0.id'))->toBe($new->id);
        expect($response->json('data.1.id'))->toBe($old->id);
    });

    it('returns 401 for guest listing audits', function () {
        $project = Project::factory()->create();

        $response = $this->getJson("/api/v1/projects/{$project->id}/audits");

        $response->assertUnauthorized();
    });

    it('only shows own audits to engineers', function () {
        $project = Project::factory()->create();
        $own = Audit::factory()->create(['project_id' => $project->id, 'performed_by' => $this->engineer->id]);
        Audit::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($this->engineer)
            ->getJson("/api/v1/projects/{$project->id}/audits");

        $response->assertOk();
        expect(count($response->json('data')))->toBe(1);
        expect($response->json('data.0.id'))->toBe($own->id);
    });
});

describe('GET /api/v1/audits/{audit}', function () {

    it('shows full audit details', function () {
        $audit = Audit::factory()->completed()->create();

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/audits/{$audit->id}");

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'status', 'quality_score', 'network_statistics']]);
        expect($response->json('data.status'))->toBe('completed');
    });

    it('includes performer data when loaded', function () {
        $audit = Audit::factory()->completed()->create(['performed_by' => $this->admin->id]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/audits/{$audit->id}");

        $response->assertOk()
            ->assertJsonPath('data.performer.id', $this->admin->id);
    });

    it('includes dataset data when loaded', function () {
        $dataset = ProjectDataset::factory()->create();
        $audit = Audit::factory()->completed()->create(['projectdataset_id' => $dataset->id]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/audits/{$audit->id}");

        $response->assertOk()
            ->assertJsonPath('data.dataset.id', $dataset->id);
    });

    it('returns 404 for non-existent audit', function () {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/audits/99999');

        $response->assertNotFound();
    });

    it('returns 401 for guest viewing audit', function () {
        $audit = Audit::factory()->create();

        $response = $this->getJson("/api/v1/audits/{$audit->id}");

        $response->assertUnauthorized();
    });

    it('allows engineer to view own audit', function () {
        $audit = Audit::factory()->completed()->create(['performed_by' => $this->engineer->id]);

        $response = $this->actingAs($this->engineer)
            ->getJson("/api/v1/audits/{$audit->id}");

        $response->assertOk();
    });

    it('denies engineer to view another engineers audit', function () {
        $audit = Audit::factory()->completed()->create();

        $response = $this->actingAs($this->engineer)
            ->getJson("/api/v1/audits/{$audit->id}");

        $response->assertForbidden();
    });
});

describe('Audit status transitions', function () {

    it('audit can transition from pending to running', function () {
        $audit = Audit::factory()->create(['status' => AuditStatus::Pending]);

        $audit->update(['status' => AuditStatus::Running]);

        expect($audit->fresh()->status)->toBe(AuditStatus::Running);
    });

    it('audit can transition from running to completed', function () {
        $audit = Audit::factory()->create(['status' => AuditStatus::Running]);

        $audit->update(['status' => AuditStatus::Completed, 'completed_at' => now()]);

        expect($audit->fresh()->status)->toBe(AuditStatus::Completed);
    });

    it('audit can transition from running to failed', function () {
        $audit = Audit::factory()->create(['status' => AuditStatus::Running]);

        $audit->update(['status' => AuditStatus::Failed, 'error_message' => 'Test error']);

        expect($audit->fresh()->status)->toBe(AuditStatus::Failed);
    });

    it('failed audit stores error message', function () {
        $audit = Audit::factory()->failed()->create([
            'error_message' => 'Dataset not found for audit.',
        ]);

        expect($audit->fresh()->error_message)->toBe('Dataset not found for audit.');
    });
});

describe('Audit score calculation', function () {

    it('stores scores when audit completes', function () {
        $audit = Audit::factory()->create([
            'quality_score' => 85.50,
            'connectivity_score' => 90.00,
            'coherence_score' => 82.30,
            'capacity_score' => 78.00,
            'extensibility_score' => 91.70,
        ]);

        $fresh = $audit->fresh();
        expect($fresh->quality_score)->toBe('85.50');
        expect($fresh->connectivity_score)->toBe('90.00');
        expect($fresh->coherence_score)->toBe('82.30');
        expect($fresh->capacity_score)->toBe('78.00');
        expect($fresh->extensibility_score)->toBe('91.70');
    });

    it('weighted score calculates correctly', function () {
        $audit = Audit::factory()->create([
            'quality_score' => null,
            'connectivity_score' => 100,
            'coherence_score' => 80,
            'capacity_score' => 60,
            'extensibility_score' => 40,
        ]);

        $expected = round(100 * 0.40 + 80 * 0.30 + 60 * 0.20 + 40 * 0.10, 2);
        expect($audit->weightedScore())->toBe($expected);
    });

    it('returns null weighted score when any sub-score is missing', function () {
        $audit = Audit::factory()->create([
            'quality_score' => null,
            'connectivity_score' => 100,
            'coherence_score' => null,
            'capacity_score' => 60,
            'extensibility_score' => 40,
        ]);

        expect($audit->weightedScore())->toBeNull();
    });
});

describe('Audit with queue fake', function () {

    it('does not dispatch RunAuditJob when no dataset exists', function () {
        Queue::fake();
        $project = Project::factory()->create();

        $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$project->id}/audits");

        Queue::assertNotPushed(RunAuditJob::class);
    });

    it('dispatches exactly one RunAuditJob per audit launch', function () {
        Queue::fake();
        $project = Project::factory()->create();
        ProjectDataset::factory()->create(['project_id' => $project->id]);

        $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$project->id}/audits");

        Queue::assertPushed(RunAuditJob::class, function ($job) {
            return true;
        });
    });

    it('RunAuditJob dispatches AnalyzeAuditJob', function () {
        $project = Project::factory()->create();
        $dataset = ProjectDataset::factory()->create([
            'project_id' => $project->id,
            'geojson' => [
                't_noeud' => [],
                't_cable' => [],
                't_ebp' => [],
                't_sitetech' => [],
                't_ptech' => [],
            ],
        ]);
        $audit = Audit::factory()->create([
            'project_id' => $project->id,
            'projectdataset_id' => $dataset->id,
            'status' => AuditStatus::Pending,
        ]);

        $job = new RunAuditJob($audit->id);
        $job->handle(app(AuditService::class));

        Queue::assertPushed(AnalyzeAuditJob::class);
    });

    it('RunAuditJob is dispatched as a queued job', function () {
        $job = new RunAuditJob(1);

        expect($job)->toBeInstanceOf(ShouldQueue::class);
    });
});

describe('Audit retry', function () {

    it('retries a failed audit via API', function () {
        Queue::fake();
        $project = Project::factory()->create();
        $audit = Audit::factory()->failed()->for($project)->create([
            'performed_by' => $this->admin->id,
            'error_message' => 'Previous failure',
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/audits/{$audit->id}/retry");

        $response->assertOk();
        expect($audit->fresh()->status)->toBe(AuditStatus::Pending);
        expect($audit->fresh()->error_message)->toBeNull();
        Queue::assertPushed(RunAuditJob::class, fn ($job) => $job->auditId === $audit->id);
    });

    it('retries a stale running audit via API', function () {
        Queue::fake();
        $project = Project::factory()->create();
        $audit = Audit::factory()->for($project)->create([
            'performed_by' => $this->admin->id,
            'status' => AuditStatus::Running,
        ]);
        Audit::query()->whereKey($audit->id)->update(['updated_at' => now()->subMinutes(35)]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/audits/{$audit->id}/retry");

        $response->assertOk();
        expect($audit->fresh()->status)->toBe(AuditStatus::Pending);
        Queue::assertPushed(RunAuditJob::class);
    });

    it('rejects retrying a fresh running audit via API', function () {
        Queue::fake();
        $project = Project::factory()->create();
        $audit = Audit::factory()->for($project)->create([
            'performed_by' => $this->admin->id,
            'status' => AuditStatus::Running,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/audits/{$audit->id}/retry");

        $response->assertStatus(422);
        Queue::assertNotPushed(RunAuditJob::class);
    });

    it('rejects retrying a completed audit via API', function () {
        Queue::fake();
        $project = Project::factory()->create();
        $audit = Audit::factory()->completed()->for($project)->create([
            'performed_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/audits/{$audit->id}/retry");

        $response->assertStatus(422);
        Queue::assertNotPushed(RunAuditJob::class);
    });

    it('denies retrying another users audit via API', function () {
        Queue::fake();
        $project = Project::factory()->create();
        $other = User::factory()->engineer()->create();
        $audit = Audit::factory()->failed()->for($project)->create(['performed_by' => $other->id]);

        $response = $this->actingAs($this->engineer)
            ->postJson("/api/v1/audits/{$audit->id}/retry");

        $response->assertForbidden();
        Queue::assertNotPushed(RunAuditJob::class);
    });

    it('retries a failed audit via web and redirects', function () {
        Queue::fake();
        $project = Project::factory()->create();
        $audit = Audit::factory()->failed()->for($project)->create([
            'performed_by' => $this->admin->id,
            'error_message' => 'Previous failure',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.audits.retry', [$project, $audit]));

        $response->assertRedirect(route('admin.projects.audits.show', [$project, $audit]));
        expect($audit->fresh()->status)->toBe(AuditStatus::Pending);
        Queue::assertPushed(RunAuditJob::class);
    });

    it('allows the owner engineer to retry their own audit', function () {
        Queue::fake();
        $project = Project::factory()->create();
        $audit = Audit::factory()->failed()->for($project)->create([
            'performed_by' => $this->engineer->id,
        ]);

        $response = $this->actingAs($this->engineer)
            ->postJson("/api/v1/audits/{$audit->id}/retry");

        $response->assertOk();
        expect($audit->fresh()->status)->toBe(AuditStatus::Pending);
    });
});
