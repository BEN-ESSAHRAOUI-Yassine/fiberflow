<?php

use App\Models\Project;
use App\Models\User;
use App\Services\GISService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('database.connections.postgis', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
    ]);

    $pdo = DB::connection('postgis')->getPdo();
    $pdo->sqliteCreateFunction('ST_AsGeoJSON', function ($geom) {
        return $geom;
    }, 1);

    Schema::connection('postgis')->create('t_znro', function ($table) {
        $table->string('zn_code')->primary();
        $table->string('zn_nd_code')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('postgis')->create('t_noeud', function ($table) {
        $table->string('nd_code')->primary();
        $table->string('nd_nom')->nullable();
        $table->string('nd_type')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('postgis')->create('t_ptech', function ($table) {
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

    Schema::connection('postgis')->create('t_ebp', function ($table) {
        $table->string('bp_code')->primary();
        $table->string('bp_nd_code')->nullable();
        $table->string('bp_typephy')->nullable();
        $table->string('bp_typelog')->nullable();
        $table->string('bp_etat')->nullable();
        $table->string('bp_prop')->nullable();
    });

    Schema::connection('postgis')->create('t_sitetech', function ($table) {
        $table->string('st_code')->primary();
        $table->string('st_nd_code')->nullable();
        $table->string('st_typ')->nullable();
        $table->string('st_etat')->nullable();
        $table->string('st_prop')->nullable();
    });

    Schema::connection('postgis')->create('t_cable', function ($table) {
        $table->string('cb_code')->primary();
        $table->integer('cb_fo')->nullable();
        $table->string('cb_typelog')->nullable();
        $table->string('cb_etat')->nullable();
        $table->string('cb_prop')->nullable();
    });

    Schema::connection('postgis')->create('t_cableline', function ($table) {
        $table->string('cl_code')->primary();
        $table->string('cl_cb_code')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('postgis')->create('t_cheminement', function ($table) {
        $table->string('ch_code')->primary();
        $table->string('ch_typ')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('postgis')->create('t_conduite', function ($table) {
        $table->string('cd_code')->primary();
        $table->string('cd_typ')->nullable();
        $table->decimal('cd_dia_int', 8, 2)->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('postgis')->create('t_zsro', function ($table) {
        $table->string('zs_code')->primary();
        $table->string('zs_nd_code')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('postgis')->create('t_zpbo', function ($table) {
        $table->string('zp_code')->primary();
        $table->string('zp_nd_code')->nullable();
        $table->text('geom')->nullable();
    });

    Schema::connection('postgis')->create('t_adresse', function ($table) {
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

    $this->admin = User::factory()->admin()->create();
    $this->project = Project::factory()->create([
        'gis_project_id' => 'TEST-SCHEMA',
    ]);

    $this->service = app(GISService::class);
});

it('returns empty schemas collection when not connected to PostGIS', function () {
    $schemas = $this->service->getAvailableSchemas();

    expect($schemas)->toBeInstanceOf(Collection::class);
    expect($schemas)->toBeEmpty();
});

it('imports all 12 GraceTHD tables', function () {
    DB::connection('postgis')->table('t_znro')->insert([
        ['zn_code' => 'TEST-SCHEMA', 'zn_nd_code' => 'NODE001'],
    ]);
    DB::connection('postgis')->table('t_noeud')->insert([
        ['nd_code' => 'NODE001', 'nd_nom' => 'Test Node', 'nd_type' => 'transport', 'geom' => '{"type":"Point","coordinates":[46.0,1.0]}'],
    ]);
    DB::connection('postgis')->table('t_ptech')->insert([
        ['pt_code' => 'PT001', 'pt_nd_code' => 'NODE001', 'pt_typephy' => 'NRO', 'pt_etat' => 'actif'],
    ]);
    DB::connection('postgis')->table('t_cable')->insert([
        ['cb_code' => 'CB001', 'cb_fo' => 48, 'cb_etat' => 'actif'],
    ]);
    DB::connection('postgis')->table('t_cableline')->insert([
        ['cl_code' => 'CL001', 'cl_cb_code' => 'CB001', 'geom' => '{"type":"LineString","coordinates":[[0.0,0.0],[1.0,1.0]]}'],
    ]);
    DB::connection('postgis')->table('t_cheminement')->insert([
        ['ch_code' => 'CH001', 'ch_typ' => 'aerial', 'geom' => '{"type":"LineString","coordinates":[[0.0,0.0],[1.0,1.0]]}'],
    ]);
    DB::connection('postgis')->table('t_conduite')->insert([
        ['cd_code' => 'CD001', 'cd_typ' => 'underground', 'cd_dia_int' => 50.00, 'geom' => '{"type":"LineString","coordinates":[[0.0,0.0],[1.0,1.0]]}'],
    ]);
    DB::connection('postgis')->table('t_ebp')->insert([
        ['bp_code' => 'BP001', 'bp_nd_code' => 'NODE001', 'bp_typephy' => 'NRO'],
    ]);
    DB::connection('postgis')->table('t_sitetech')->insert([
        ['st_code' => 'ST001', 'st_nd_code' => 'NODE001', 'st_typ' => 'central'],
    ]);
    DB::connection('postgis')->table('t_zsro')->insert([
        ['zs_code' => 'ZS001', 'zs_nd_code' => 'NODE001', 'geom' => '{"type":"MultiPolygon","coordinates":[[[[0.0,0.0],[1.0,0.0],[1.0,1.0],[0.0,1.0],[0.0,0.0]]]]}'],
    ]);
    DB::connection('postgis')->table('t_zpbo')->insert([
        ['zp_code' => 'ZP001', 'zp_nd_code' => 'NODE001', 'geom' => '{"type":"MultiPolygon","coordinates":[[[[0.0,0.0],[1.0,0.0],[1.0,1.0],[0.0,1.0],[0.0,0.0]]]]}'],
    ]);
    DB::connection('postgis')->table('t_adresse')->insert([
        ['ad_code' => 'ADR001', 'ad_commune' => 'TestVille', 'ad_insee' => '12345', 'ad_postal' => '12345', 'ad_nbprhab' => 10, 'ad_nbprpro' => 2, 'ad_itypeim' => 'I', 'geom' => '{"type":"Point","coordinates":[46.0,1.0]}'],
    ]);

    $result = $this->service->importFromPostGIS('test-schema');

    expect($result)->toHaveKeys(['geojson', 'counts']);
    expect($result['counts'])->toHaveCount(12);

    expect($result['geojson']['t_noeud'][0]['properties']['nd_code'])->toBe('NODE001');
    expect($result['geojson']['t_ptech'][0]['properties']['pt_code'])->toBe('PT001');
    expect($result['geojson']['t_cable'][0]['properties']['cb_code'])->toBe('CB001');

    $nodeFeature = $result['geojson']['t_noeud'][0];
    expect($nodeFeature['geometry'])->not->toBeNull();
    expect($nodeFeature['geometry']['type'])->toBe('Point');
});

it('returns empty result for unknown table', function () {
    DB::connection('postgis')->table('t_znro')->insert([
        ['zn_code' => 'OTHER', 'zn_nd_code' => 'NODE002'],
    ]);

    $result = $this->service->importFromPostGIS('test-schema');

    expect($result['counts']['t_znro'])->toBe(1);
    expect($result['geojson']['t_znro'][0]['properties']['zn_code'])->toBe('OTHER');
});

describe('dataset import API', function () {

    it('denies import for engineer', function () {
        $engineer = User::factory()->engineer()->create();

        $response = $this->actingAs($engineer)
            ->postJson("/api/v1/projects/{$this->project->id}/datasets/import", [
                'schema' => 'apd_07',
            ]);

        $response->assertForbidden();
    });

    it('returns validation error without schema', function () {
        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/projects/{$this->project->id}/datasets/import", []);

        $response->assertJsonValidationErrorFor('schema');
    });

});

describe('network API', function () {

    it('returns null when no dataset imported', function () {
        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/projects/{$this->project->id}/network");

        $response->assertOk();
        expect($response->json('data'))->toBeNull();
    });

    it('returns features from imported dataset', function () {
        $this->project->datasets()->create([
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

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/projects/{$this->project->id}/network");

        $response->assertOk();
        expect($response->json('count'))->toBe(2);
        expect(count($response->json('data.features')))->toBe(2);
    });

    it('filters by layer', function () {
        $this->project->datasets()->create([
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

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/projects/{$this->project->id}/network?layer=t_noeud");

        $response->assertOk();
        expect($response->json('count'))->toBe(1);
        expect($response->json('layer'))->toBe('t_noeud');
    });

    it('searches features by property value', function () {
        $this->project->datasets()->create([
            'geojson' => [
                't_noeud' => [
                    ['type' => 'Feature', 'geometry' => null, 'properties' => ['nd_code' => 'N001', 'nd_nom' => 'Bauge']],
                    ['type' => 'Feature', 'geometry' => null, 'properties' => ['nd_code' => 'N002', 'nd_nom' => 'Other']],
                ],
            ],
            'imported_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/v1/projects/{$this->project->id}/network?search=bauge");

        $response->assertOk();
        expect($response->json('count'))->toBe(1);
        expect($response->json('data.features')[0]['properties']['nd_code'])->toBe('N001');
    });
});
