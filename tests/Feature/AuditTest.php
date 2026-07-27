<?php

use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectDataset;
use App\Models\User;
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

    it('returns 422 when project has no dataset', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$project->id}/audits");

        $response->assertStatus(422)
            ->assertJson(['message' => true]);
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

    it('returns 404 for non-existent audit', function () {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/audits/99999');

        $response->assertNotFound();
    });
});
