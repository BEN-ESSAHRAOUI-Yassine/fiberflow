<?php

use App\Enums\ProjectStatus;
use App\Models\Audit;
use App\Models\Project;
use App\Models\User;
use App\Services\GISService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function gisPayload(array $overrides = []): array
{
    return array_merge([
        'host' => '127.0.0.1',
        'port' => '5432',
        'database' => 'test-gis',
        'username' => 'fiberflow',
        'password' => 'secret',
    ], $overrides);
}

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->engineer = User::factory()->engineer()->create();
    $this->project = Project::factory()->create();
});

describe('web connection form', function () {

    it('lists candidate schemas after a successful connection test', function () {
        $this->mock(GISService::class, function ($mock) {
            $mock->shouldReceive('testConnection')->once()->andReturn(true);
            $mock->shouldReceive('getAvailableSchemas')->once()->andReturn(collect([
                (object) ['schema' => 'apd_07', 'label' => 'apd_07'],
                (object) ['schema' => 'rec_08', 'label' => 'rec_08'],
            ]));
        });

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.datasets.test-connection', $this->project), gisPayload());

        $response->assertRedirect()
            ->assertSessionHas('connection_ok')
            ->assertSessionHas('schemas', fn ($schemas) => collect($schemas)->pluck('schema')->all() === ['apd_07', 'rec_08']);
    });

    it('renders the import form when the session stores schemas as an array', function () {
        session(['schemas' => [['schema' => 'apd_07', 'label' => 'apd_07']]]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.projects.datasets.import', $this->project));

        $response->assertOk();
    });

    it('reports a failed connection without leaking the password', function () {
        $this->mock(GISService::class, function ($mock) {
            $mock->shouldReceive('testConnection')->once()->andReturn(false);
        });

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.datasets.test-connection', $this->project), gisPayload());

        $response->assertRedirect()
            ->assertSessionHasErrors('connection')
            ->assertSessionDoesntHaveErrors(['host', 'port', 'database', 'username']);

        expect(session('_old_input')['password'] ?? null)->toBeNull();
        expect(session('_old_input')['host'] ?? null)->toBe('127.0.0.1');
    });

    it('validates the connection fields', function () {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.datasets.test-connection', $this->project), []);

        $response->assertSessionHasErrors(['host', 'port', 'database', 'username', 'password']);
    });

    it('denies connection testing to engineers', function () {
        $response = $this->actingAs($this->engineer)
            ->post(route('admin.projects.datasets.test-connection', $this->project), gisPayload());

        $response->assertForbidden();
    });
});

describe('web dataset import', function () {

    beforeEach(function () {
        $this->mock(GISService::class, function ($mock) {
            $mock->shouldReceive('testConnection')->andReturn(true);
            $mock->shouldReceive('getAvailableSchemas')->andReturn(collect([
                (object) ['schema' => 'apd_07', 'label' => 'apd_07'],
            ]));
            $mock->shouldReceive('importFromPostGIS')->withArgs(function ($connection, $schema) {
                return $schema === 'apd_07' && $connection['password'] === 'secret';
            })->andReturn([
                'geojson' => ['t_noeud' => []],
                'counts' => ['t_noeud' => 3],
            ]);
        });
    });

    it('imports a dataset, saves config and advances the status', function () {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.datasets.import.store', $this->project), gisPayload(['schema' => 'apd_07']));

        $response->assertRedirect(route('admin.projects.show', $this->project))
            ->assertSessionHas('success');

        $project = $this->project->fresh();

        expect($project->datasets()->count())->toBe(1);
        expect($project->gis_host)->toBe('127.0.0.1');
        expect($project->gis_port)->toBe('5432');
        expect($project->gis_database)->toBe('test-gis');
        expect($project->gis_schema)->toBe('apd_07');
        expect($project->gis_username)->toBe('fiberflow');
        expect($project->getAttribute('gis_password'))->toBeNull();
        expect($project->status)->toBe(ProjectStatus::InProgress);
    });

    it('does not move the status backward on re-import', function () {
        $this->project->update(['status' => ProjectStatus::Audited->value]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.datasets.import.store', $this->project), gisPayload(['schema' => 'apd_07']));

        $response->assertRedirect();
        expect($this->project->fresh()->status)->toBe(ProjectStatus::Audited);
    });

    it('rejects a schema that is not available on the server', function () {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.datasets.import.store', $this->project), gisPayload(['schema' => 'wrong_schema']));

        $response->assertSessionHasErrors('schema');
        expect($this->project->datasets()->count())->toBe(0);
        expect($this->project->fresh()->status)->toBe(ProjectStatus::Draft);
    });

    it('fails cleanly when the connection is broken', function () {
        $this->mock(GISService::class, function ($mock) {
            $mock->shouldReceive('testConnection')->once()->andReturn(false);
        });

        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.datasets.import.store', $this->project), gisPayload(['schema' => 'apd_07']));

        $response->assertSessionHasErrors('connection');
        expect($this->project->datasets()->count())->toBe(0);
    });

    it('denies import to engineers', function () {
        $response = $this->actingAs($this->engineer)
            ->post(route('admin.projects.datasets.import.store', $this->project), gisPayload(['schema' => 'apd_07']));

        $response->assertForbidden();
    });
});

describe('API connection test', function () {

    it('returns the candidate schemas', function () {
        $this->mock(GISService::class, function ($mock) {
            $mock->shouldReceive('testConnection')->once()->andReturn(true);
            $mock->shouldReceive('getAvailableSchemas')->once()->andReturn(collect([
                (object) ['schema' => 'apd_07', 'label' => 'apd_07'],
            ]));
        });

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$this->project->id}/datasets/test-connection", gisPayload());

        $response->assertOk()
            ->assertJsonPath('data.schemas', ['apd_07']);
    });

    it('returns 422 when the connection fails', function () {
        $this->mock(GISService::class, function ($mock) {
            $mock->shouldReceive('testConnection')->once()->andReturn(false);
        });

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$this->project->id}/datasets/test-connection", gisPayload());

        $response->assertStatus(422)
            ->assertJsonValidationErrors('connection');
    });
});

describe('API dataset import', function () {

    beforeEach(function () {
        $this->mock(GISService::class, function ($mock) {
            $mock->shouldReceive('testConnection')->andReturn(true);
            $mock->shouldReceive('getAvailableSchemas')->andReturn(collect([
                (object) ['schema' => 'apd_07', 'label' => 'apd_07'],
            ]));
            $mock->shouldReceive('importFromPostGIS')->andReturn([
                'geojson' => ['t_noeud' => []],
                'counts' => ['t_noeud' => 3, 't_cable' => 2],
            ]);
        });
    });

    it('imports the dataset and advances the status', function () {
        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$this->project->id}/datasets/import", gisPayload(['schema' => 'apd_07']));

        $response->assertStatus(201)
            ->assertJsonPath('data.counts.t_noeud', 3);

        $project = $this->project->fresh();

        expect($project->datasets()->count())->toBe(1);
        expect($project->gis_schema)->toBe('apd_07');
        expect($project->getAttribute('gis_password'))->toBeNull();
        expect($project->status)->toBe(ProjectStatus::InProgress);
    });

    it('denies import to engineers', function () {
        $response = $this->actingAs($this->engineer)
            ->postJson("/api/v1/projects/{$this->project->id}/datasets/import", gisPayload(['schema' => 'apd_07']));

        $response->assertForbidden();
    });
});

describe('per-user project status', function () {

    it('shows audited for an engineer who completed an audit', function () {
        $engineer = User::factory()->engineer()->create();
        $project = Project::factory()->create(['status' => 'draft']);
        Audit::factory()->completed()->create([
            'project_id' => $project->id,
            'performed_by' => $engineer->id,
        ]);

        expect($project->personalStatus($engineer))->toBe(ProjectStatus::Audited);
    });

    it('keeps the stored status for an engineer without completed audits', function () {
        $engineer = User::factory()->engineer()->create();
        $project = Project::factory()->create(['status' => 'in_progress']);

        expect($project->personalStatus($engineer))->toBe(ProjectStatus::InProgress);
    });

    it('ignores failed audits for the personal status', function () {
        $engineer = User::factory()->engineer()->create();
        $project = Project::factory()->create(['status' => 'draft']);
        Audit::factory()->failed()->create([
            'project_id' => $project->id,
            'performed_by' => $engineer->id,
        ]);

        expect($project->personalStatus($engineer))->toBe(ProjectStatus::Draft);
    });

    it('counts a personal audit even when the stored status is lower', function () {
        $engineer = User::factory()->engineer()->create();
        $project = Project::factory()->create(['status' => 'draft']);
        Audit::factory()->completed()->create([
            'project_id' => $project->id,
            'performed_by' => $engineer->id,
        ]);

        expect($project->personalStatus($engineer))->toBe(ProjectStatus::Audited);
        expect($project->personalStatus(User::factory()->engineer()->create()))->toBe(ProjectStatus::Draft);
    });

    it('advances the status forward only', function () {
        $project = Project::factory()->create(['status' => 'audited']);

        expect($project->advanceTo(ProjectStatus::InProgress))->toBeFalse();
        expect($project->fresh()->status)->toBe(ProjectStatus::Audited);
        expect($project->advanceTo(ProjectStatus::Validated))->toBeTrue();
        expect($project->fresh()->status)->toBe(ProjectStatus::Validated);
    });
});
