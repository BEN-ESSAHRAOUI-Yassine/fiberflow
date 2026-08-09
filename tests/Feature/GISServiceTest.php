<?php

use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectDataset;
use App\Models\User;
use App\Services\GISService;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->gisDbFile = tempnam(sys_get_temp_dir(), 'ffgis_');
    $this->gisConnection = [
        'host' => 'test',
        'port' => '5432',
        'database' => 'test-gis',
        'schema' => 'test-schema',
        'username' => 'test',
        'password' => 'test',
    ];

    config()->set('database.connections.ffgis', [
        'driver' => 'sqlite_ffgis',
        'database' => $this->gisDbFile,
        'prefix' => '',
    ]);

    DB::extend('sqlite_ffgis', function ($config) {
        $pdo = new PDO('sqlite:'.$config['database']);
        $pdo->exec("ATTACH DATABASE '".$config['database'].".catalog' AS information_schema");
        $pdo->sqliteCreateFunction('ST_AsGeoJSON', fn ($geom) => $geom, 1);
        $pdo->sqliteCreateFunction('ST_Transform', fn ($geom, $srid) => $geom, 2);

        return new SQLiteConnection($pdo, $config['database'], $config['prefix'], $config);
    });

    $this->service = app(GISService::class);

    createGraceThdTables();
});

afterEach(function () {
    @unlink($this->gisDbFile);
    @unlink($this->gisDbFile.'.catalog');
});

function createGraceThdTables(): void
{
    Schema::connection('ffgis')->create('t_znro', function ($table) {
        $table->string('zn_code')->primary();
        $table->string('zn_nd_code')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('ffgis')->create('t_noeud', function ($table) {
        $table->string('nd_code')->primary();
        $table->string('nd_nom')->nullable();
        $table->string('nd_type')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('ffgis')->create('t_ptech', function ($table) {
        $table->string('pt_code')->primary();
        $table->string('pt_nd_code')->nullable();
        $table->string('pt_typephy')->nullable();
        $table->string('pt_typelog')->nullable();
        $table->string('pt_etat')->nullable();
        $table->string('pt_avct')->nullable();
        $table->string('pt_nature')->nullable();
        $table->string('pt_prop')->nullable();
        $table->string('pt_gest')->nullable();
    });

    Schema::connection('ffgis')->create('t_ebp', function ($table) {
        $table->string('bp_code')->primary();
        $table->string('bp_nd_code')->nullable();
        $table->string('bp_typephy')->nullable();
        $table->string('bp_typelog')->nullable();
        $table->string('bp_etat')->nullable();
        $table->string('bp_prop')->nullable();
    });

    Schema::connection('ffgis')->create('t_sitetech', function ($table) {
        $table->string('st_code')->primary();
        $table->string('st_nd_code')->nullable();
        $table->string('st_typ')->nullable();
        $table->string('st_etat')->nullable();
        $table->string('st_prop')->nullable();
    });

    Schema::connection('ffgis')->create('t_cable', function ($table) {
        $table->string('cb_code')->primary();
        $table->integer('cb_fo')->nullable();
        $table->string('cb_typelog')->nullable();
        $table->string('cb_etat')->nullable();
        $table->string('cb_prop')->nullable();
    });

    Schema::connection('ffgis')->create('t_cableline', function ($table) {
        $table->string('cl_code')->primary();
        $table->string('cl_cb_code')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('ffgis')->create('t_cheminement', function ($table) {
        $table->string('ch_code')->primary();
        $table->string('ch_typ')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('ffgis')->create('t_conduite', function ($table) {
        $table->string('cd_code')->primary();
        $table->string('cd_typ')->nullable();
        $table->decimal('cd_dia_int', 8, 2)->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('ffgis')->create('t_zsro', function ($table) {
        $table->string('zs_code')->primary();
        $table->string('zs_nd_code')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('ffgis')->create('t_zpbo', function ($table) {
        $table->string('zp_code')->primary();
        $table->string('zp_nd_code')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('ffgis')->create('t_adresse', function ($table) {
        $table->string('ad_code')->primary();
        $table->string('ad_commune')->nullable();
        $table->string('ad_insee')->nullable();
        $table->string('ad_postal')->nullable();
        $table->integer('ad_nbprhab')->nullable();
        $table->integer('ad_nbprpro')->nullable();
        $table->integer('ad_nblhab')->nullable();
        $table->integer('ad_nblpro')->nullable();
        $table->string('ad_itypeim')->nullable();
        $table->boolean('ad_imneuf')->nullable();
        $table->text('geom')->nullable();
    });
}

function createInformationSchema(): void
{
    Schema::connection('ffgis')->create('information_schema.schemata', function ($table) {
        $table->string('schema_name');
    });

    Schema::connection('ffgis')->create('information_schema.tables', function ($table) {
        $table->string('table_schema');
        $table->string('table_name');
    });
}

it('returns empty schemas collection when no candidate schemas exist', function () {
    $schemas = $this->service->getAvailableSchemas($this->gisConnection);

    expect($schemas)->toBeInstanceOf(Collection::class);
    expect($schemas)->toBeEmpty();
});

it('returns true when the connection succeeds', function () {
    expect($this->service->testConnection($this->gisConnection))->toBeTrue();
});

it('returns false when the connection fails', function () {
    config()->set('database.connections.ffgis', []);

    expect($this->service->testConnection($this->gisConnection))->toBeFalse();
});

it('lists only schemas containing expected tables', function () {
    createInformationSchema();

    DB::connection('ffgis')->table('information_schema.schemata')->insert([
        ['schema_name' => 'apd_07'],
        ['schema_name' => 'apd_08'],
        ['schema_name' => 'pg_catalog'],
        ['schema_name' => 'random_schema'],
    ]);

    DB::connection('ffgis')->table('information_schema.tables')->insert([
        ['table_schema' => 'apd_07', 'table_name' => 't_noeud'],
        ['table_schema' => 'apd_08', 'table_name' => 't_cable'],
        ['table_schema' => 'random_schema', 'table_name' => 'users'],
    ]);

    $schemas = $this->service->getAvailableSchemas($this->gisConnection);

    expect($schemas->pluck('schema')->all())->toBe(['apd_07', 'apd_08']);
});

it('imports all 12 GraceTHD tables', function () {
    DB::connection('ffgis')->table('t_znro')->insert([
        ['zn_code' => 'TEST-SCHEMA', 'zn_nd_code' => 'NODE001'],
    ]);
    DB::connection('ffgis')->table('t_noeud')->insert([
        ['nd_code' => 'NODE001', 'nd_nom' => 'Test Node', 'nd_type' => 'transport', 'geom' => '{"type":"Point","coordinates":[46.0,1.0]}'],
    ]);
    DB::connection('ffgis')->table('t_ptech')->insert([
        ['pt_code' => 'PT001', 'pt_nd_code' => 'NODE001', 'pt_typephy' => 'NRO', 'pt_etat' => 'actif'],
    ]);
    DB::connection('ffgis')->table('t_cable')->insert([
        ['cb_code' => 'CB001', 'cb_fo' => 48, 'cb_etat' => 'actif'],
    ]);
    DB::connection('ffgis')->table('t_cableline')->insert([
        ['cl_code' => 'CL001', 'cl_cb_code' => 'CB001', 'geom' => '{"type":"LineString","coordinates":[[0.0,0.0],[1.0,1.0]]}'],
    ]);
    DB::connection('ffgis')->table('t_cheminement')->insert([
        ['ch_code' => 'CH001', 'ch_typ' => 'aerial', 'geom' => '{"type":"LineString","coordinates":[[0.0,0.0],[1.0,1.0]]}'],
    ]);
    DB::connection('ffgis')->table('t_conduite')->insert([
        ['cd_code' => 'CD001', 'cd_typ' => 'underground', 'cd_dia_int' => 50.00, 'geom' => '{"type":"LineString","coordinates":[[0.0,0.0],[1.0,1.0]]}'],
    ]);
    DB::connection('ffgis')->table('t_ebp')->insert([
        ['bp_code' => 'BP001', 'bp_nd_code' => 'NODE001', 'bp_typephy' => 'NRO'],
    ]);
    DB::connection('ffgis')->table('t_sitetech')->insert([
        ['st_code' => 'ST001', 'st_nd_code' => 'NODE001', 'st_typ' => 'central'],
    ]);
    DB::connection('ffgis')->table('t_zsro')->insert([
        ['zs_code' => 'ZS001', 'zs_nd_code' => 'NODE001', 'geom' => '{"type":"MultiPolygon","coordinates":[[[[0.0,0.0],[1.0,0.0],[1.0,1.0],[0.0,1.0],[0.0,0.0]]]]}'],
    ]);
    DB::connection('ffgis')->table('t_zpbo')->insert([
        ['zp_code' => 'ZP001', 'zp_nd_code' => 'NODE001', 'geom' => '{"type":"MultiPolygon","coordinates":[[[[0.0,0.0],[1.0,0.0],[1.0,1.0],[0.0,1.0],[0.0,0.0]]]]}'],
    ]);
    DB::connection('ffgis')->table('t_adresse')->insert([
        ['ad_code' => 'ADR001', 'ad_commune' => 'TestVille', 'ad_insee' => '12345', 'ad_postal' => '12345', 'ad_nbprhab' => 10, 'ad_nbprpro' => 2, 'ad_itypeim' => 'I', 'geom' => '{"type":"Point","coordinates":[46.0,1.0]}'],
    ]);

    $result = $this->service->importFromPostGIS($this->gisConnection, 'test-schema');

    expect($result)->toHaveKeys(['geojson', 'counts']);
    expect($result['counts'])->toHaveCount(12);

    expect($result['geojson']['t_noeud'][0]['properties']['nd_code'])->toBe('NODE001');
    expect($result['geojson']['t_ptech'][0]['properties']['pt_code'])->toBe('PT001');
    expect($result['geojson']['t_cable'][0]['properties']['cb_code'])->toBe('CB001');

    $nodeFeature = $result['geojson']['t_noeud'][0];
    expect($nodeFeature['geometry'])->not->toBeNull();
    expect($nodeFeature['geometry']['type'])->toBe('Point');
});

it('returns rows for tables with data only', function () {
    DB::connection('ffgis')->table('t_znro')->insert([
        ['zn_code' => 'OTHER', 'zn_nd_code' => 'NODE002'],
    ]);

    $result = $this->service->importFromPostGIS($this->gisConnection, 'test-schema');

    expect($result['counts']['t_znro'])->toBe(1);
    expect($result['geojson']['t_znro'][0]['properties']['zn_code'])->toBe('OTHER');
});

describe('dataset import API', function () {

    it('denies import for engineer', function () {
        $engineer = User::factory()->engineer()->create();
        $project = Project::factory()->create();

        $response = $this->actingAs($engineer)
            ->postJson("/api/v1/projects/{$project->id}/datasets/import", [
                'host' => '127.0.0.1',
                'port' => '5432',
                'database' => 'test-gis',
                'username' => 'test',
                'password' => 'test',
                'schema' => 'test-schema',
            ]);

        $response->assertForbidden();
    });

    it('returns validation error without connection fields', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->postJson("/api/v1/projects/{$project->id}/datasets/import", []);

        $response->assertJsonValidationErrorFor('host')
            ->assertJsonValidationErrorFor('schema');
    });

});

describe('re-import behavior', function () {

    it('creates a new dataset record on re-import', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        ProjectDataset::factory()->create([
            'project_id' => $project->id,
            'imported_at' => now()->subDay(),
        ]);

        expect($project->datasets()->count())->toBe(1);

        $project->datasets()->create([
            'geojson' => ['t_noeud' => [['properties' => ['nd_code' => 'N001']]]],
            'imported_at' => now(),
        ]);

        expect($project->datasets()->count())->toBe(2);
    });

    it('soft-deletes previous dataset on re-import', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        $oldDataset = ProjectDataset::factory()->create([
            'project_id' => $project->id,
            'imported_at' => now()->subDay(),
        ]);

        // Simulate re-import: delete old, create new
        $oldDataset->delete();
        $newDataset = $project->datasets()->create([
            'geojson' => ['t_noeud' => [['properties' => ['nd_code' => 'N002']]]],
            'imported_at' => now(),
        ]);

        // Only new dataset visible in normal query
        expect($project->datasets()->count())->toBe(1);
        expect($project->datasets()->first()->id)->toBe($newDataset->id);

        // Old dataset is gone
        expect(ProjectDataset::find($oldDataset->id))->toBeNull();
    });
});

describe('dataset API metadata', function () {

    it('returns dataset list for project', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        ProjectDataset::factory()->create(['project_id' => $project->id]);
        ProjectDataset::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->getJson("/api/v1/projects/{$project->id}/datasets");

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    });

    it('returns dataset details with geojson', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        $dataset = ProjectDataset::factory()->create([
            'project_id' => $project->id,
            'geojson' => ['t_noeud' => [['properties' => ['nd_code' => 'N001']]]],
        ]);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->getJson("/api/v1/projects/{$project->id}/datasets/{$dataset->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $dataset->id)
            ->assertJsonPath('data.geojson.t_noeud.0.properties.nd_code', 'N001');
    });

    it('returns 404 for dataset belonging to another project', function () {
        $project1 = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        $project2 = Project::factory()->create(['gis_project_id' => 'OTHER-SCHEMA']);
        $dataset = ProjectDataset::factory()->create(['project_id' => $project2->id]);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->getJson("/api/v1/projects/{$project1->id}/datasets/{$dataset->id}");

        $response->assertNotFound();
    });

    it('deletes a dataset without audits', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        $dataset = ProjectDataset::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->deleteJson("/api/v1/projects/{$project->id}/datasets/{$dataset->id}");

        $response->assertNoContent();
        expect(ProjectDataset::find($dataset->id))->toBeNull();
    });

    it('returns 422 when deleting a dataset referenced by audits', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        $dataset = ProjectDataset::factory()->create(['project_id' => $project->id]);
        Audit::factory()->for($project)->create([
            'projectdataset_id' => $dataset->id,
        ]);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->deleteJson("/api/v1/projects/{$project->id}/datasets/{$dataset->id}");

        $response->assertStatus(422);
        expect($response->json('message'))->toContain('audits');
        expect(ProjectDataset::find($dataset->id))->not->toBeNull();
    });
});

describe('audit without dataset', function () {

    it('returns 422 when launching audit without dataset', function () {
        $project = Project::factory()->create();

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->postJson("/api/v1/projects/{$project->id}/audits");

        $response->assertStatus(422);
        expect($response->json('message'))->toContain('no dataset');
    });

    it('allows audit launch when dataset exists', function () {
        Queue::fake();
        $project = Project::factory()->create();
        ProjectDataset::factory()->create(['project_id' => $project->id]);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->postJson("/api/v1/projects/{$project->id}/audits");

        $response->assertStatus(202);
    });
});

describe('network API', function () {

    it('returns null when no dataset imported', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->getJson("/api/v1/projects/{$project->id}/network");

        $response->assertOk();
        expect($response->json('data'))->toBeNull();
    });

    it('returns features from imported dataset', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        $project->datasets()->create([
            'geojson' => [
                't_noeud' => [
                    ['type' => 'Feature', 'geometry' => null, 'properties' => ['nd_code' => 'N001']],
                ],
                't_cable' => [
                    ['type' => 'Feature', 'geometry' => null, 'properties' => ['cb_code' => 'C001']],
                ],
            ],
            'imported_at' => now(),
        ]);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->getJson("/api/v1/projects/{$project->id}/network");

        $response->assertOk();
        expect($response->json('count'))->toBe(2);
        expect(count($response->json('data.features')))->toBe(2);
    });

    it('filters by layer', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        $project->datasets()->create([
            'geojson' => [
                't_noeud' => [
                    ['type' => 'Feature', 'geometry' => null, 'properties' => ['nd_code' => 'N001']],
                ],
                't_cable' => [
                    ['type' => 'Feature', 'geometry' => null, 'properties' => ['cb_code' => 'C001']],
                ],
            ],
            'imported_at' => now(),
        ]);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->getJson("/api/v1/projects/{$project->id}/network?layer=t_noeud");

        $response->assertOk();
        expect($response->json('count'))->toBe(1);
        expect($response->json('layer'))->toBe('t_noeud');
    });

    it('searches features by property value', function () {
        $project = Project::factory()->create(['gis_project_id' => 'TEST-SCHEMA']);
        $project->datasets()->create([
            'geojson' => [
                't_noeud' => [
                    ['type' => 'Feature', 'geometry' => null, 'properties' => ['nd_code' => 'N001', 'nd_nom' => 'Bauge']],
                    ['type' => 'Feature', 'geometry' => null, 'properties' => ['nd_code' => 'N002', 'nd_nom' => 'Other']],
                ],
            ],
            'imported_at' => now(),
        ]);

        $response = $this->actingAs($this->admin ?? User::factory()->admin()->create())
            ->getJson("/api/v1/projects/{$project->id}/network?search=bauge");

        $response->assertOk();
        expect($response->json('count'))->toBe(1);
        expect($response->json('data.features')[0]['properties']['nd_code'])->toBe('N001');
    });
});
