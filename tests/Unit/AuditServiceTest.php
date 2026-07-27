<?php

use App\Enums\AuditStatus;
use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectDataset;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(AuditService::class);
    $this->user = User::factory()->admin()->create();
});

it('calculates correct score interpretation', function () {
    expect($this->service->interpretScore(95))->toBe('Excellent');
    expect($this->service->interpretScore(85))->toBe('Good');
    expect($this->service->interpretScore(60))->toBe('Acceptable');
    expect($this->service->interpretScore(30))->toBe('Non-compliant');
    expect($this->service->interpretScore(90))->toBe('Excellent');
    expect($this->service->interpretScore(75))->toBe('Good');
    expect($this->service->interpretScore(50))->toBe('Acceptable');
});

it('calculates correct capacity threshold', function () {
    expect($this->service->getCapacityThreshold(95))->toBe('Critical');
    expect($this->service->getCapacityThreshold(80))->toBe('Warning');
    expect($this->service->getCapacityThreshold(50))->toBe('Normal');
    expect($this->service->getCapacityThreshold(100))->toBe('Critical');
    expect($this->service->getCapacityThreshold(79))->toBe('Normal');
});

it('calculates scores from anomalies and stats', function () {
    $geojson = [
        't_noeud' => [['properties' => ['id' => 1]], ['properties' => ['id' => 2]]],
        't_cable' => [
            ['properties' => ['nb_fibres' => 48, 'nb_fibres_utilisees' => 12]],
        ],
    ];
    $stats = [
        'total_fibers' => 48,
        'used_fibers' => 12,
        'spare_fibers' => 36,
        'occupation_rate' => 25.0,
    ];
    $anomalies = [];

    $scores = $this->service->calculateScores($anomalies, $stats, $geojson);

    expect($scores['connectivity'])->toBe(100);
    expect($scores['coherence'])->toBe(100);
    expect($scores['extensibility'])->toBe(75);
    expect($scores['overall'])->toBeGreaterThan(80);
});

it('assigns lower scores when anomalies exist', function () {
    $geojson = [
        't_noeud' => [['properties' => ['id' => 1]], ['properties' => ['id' => 2]]],
    ];
    $stats = ['total_fibers' => 0, 'used_fibers' => 0, 'spare_fibers' => 0, 'occupation_rate' => 0];
    $anomalies = [
        ['type' => 'distribution', 'severity' => 'critical', 'message' => '2 orphan PBO(s) without BO connection'],
        ['type' => 'distribution', 'severity' => 'critical', 'message' => '1 BO(s) without parent SRO'],
    ];

    $scores = $this->service->calculateScores($anomalies, $stats, $geojson);

    expect($scores['connectivity'])->toBeLessThan(100);
    expect($scores['coherence'])->toBeLessThan(100);
});

it('runs audit with no anomalies on empty geojson', function () {
    $project = Project::factory()->create();
    $dataset = ProjectDataset::factory()->create([
        'project_id' => $project->id,
        'geojson' => [],
    ]);
    $audit = Audit::factory()->create([
        'project_id' => $project->id,
        'projectdataset_id' => $dataset->id,
        'performed_by' => $this->user->id,
        'status' => AuditStatus::Pending,
    ]);

    $result = $this->service->runAudit($dataset, $audit);

    expect($result->status->value)->toBe('completed');
    expect($result->quality_score)->not->toBeNull();
    expect($result->network_statistics)->toBeArray();
});

it('detects transport anomalies when NRO exists without SRO', function () {
    $project = Project::factory()->transport()->create();
    $dataset = ProjectDataset::factory()->create([
        'project_id' => $project->id,
        'geojson' => [
            't_znro' => [['properties' => ['id' => 'NRO-001']]],
            't_zsro' => [],
        ],
    ]);
    $audit = Audit::factory()->create([
        'project_id' => $project->id,
        'projectdataset_id' => $dataset->id,
        'performed_by' => $this->user->id,
        'status' => AuditStatus::Pending,
    ]);

    $result = $this->service->runAudit($dataset, $audit);

    expect($result->anomaly_count)->toBeGreaterThan(0);
});

it('detects distribution anomalies for orphan PBO', function () {
    $project = Project::factory()->distribution()->create();
    $dataset = ProjectDataset::factory()->create([
        'project_id' => $project->id,
        'geojson' => [
            't_noeud' => [
                ['properties' => ['id' => 'PBO-001', 'type' => 'PBO']],
                ['properties' => ['id' => 'PBO-002', 'type' => 'PBO', 'id_bo' => 'BO-001']],
            ],
        ],
    ]);
    $audit = Audit::factory()->create([
        'project_id' => $project->id,
        'projectdataset_id' => $dataset->id,
        'performed_by' => $this->user->id,
        'status' => AuditStatus::Pending,
    ]);

    $result = $this->service->runAudit($dataset, $audit);

    expect($result->anomaly_count)->toBeGreaterThan(0);
    expect($result->critical_anomaly_count)->toBeGreaterThan(0);
});

it('extracts detailed network statistics with correct structure', function () {
    $project = Project::factory()->distribution()->create();
    $dataset = ProjectDataset::factory()->create([
        'project_id' => $project->id,
        'geojson' => [
            't_noeud' => [
                ['properties' => ['nd_type' => 'PT']],
                ['properties' => ['nd_type' => 'SRO']],
                ['properties' => ['nd_type' => 'BO']],
                ['properties' => ['nd_type' => 'PBO']],
            ],
            't_cable' => [
                ['properties' => ['cb_capafo' => 144, 'cb_fo_util' => 48, 'cb_lgreel' => '120.5', 'cb_typelog' => 'DI', 'cb_rf_code' => 'RF000000000029']],
                ['properties' => ['cb_capafo' => 144, 'cb_fo_util' => 72, 'cb_lgreel' => '80.3', 'cb_typelog' => 'TR', 'cb_rf_code' => 'RF000000000029']],
                ['properties' => ['cb_capafo' => 48, 'cb_fo_util' => 12, 'cb_lgreel' => '200.0', 'cb_typelog' => 'DI', 'cb_rf_code' => 'RF000000000047']],
            ],
            't_cableline' => [
                ['properties' => ['cl_long' => '110.0']],
                ['properties' => ['cl_long' => '75.0']],
                ['properties' => ['cl_long' => '190.0']],
            ],
            't_cheminement' => [
                ['properties' => ['cm_long' => '500.0', 'cm_typ_imp' => '1', 'cm_typelog' => 'DI']],
                ['properties' => ['cm_long' => '300.0', 'cm_typ_imp' => '2', 'cm_typelog' => 'DI']],
            ],
            't_ebp' => [
                ['properties' => ['bp_typephy' => 'B144', 'bp_ca_nb' => '7']],
                ['properties' => ['bp_typephy' => 'B72', 'bp_ca_nb' => '5']],
            ],
            't_sitetech' => [
                ['properties' => ['st_typelog' => 'NRO', 'st_typephy' => 'BAT']],
            ],
            't_ptech' => [
                ['properties' => ['pt_typephy' => 'C', 'pt_statut' => 'REC']],
                ['properties' => ['pt_typephy' => 'C', 'pt_statut' => 'REC']],
            ],
            't_conduite' => [
                ['properties' => ['cd_type' => 'NC']],
            ],
            't_zsro' => [
                ['properties' => ['zs_nblogmt' => '354', 'zs_capamax' => '600']],
            ],
            't_zpbo' => [
                ['properties' => []],
            ],
        ],
    ]);
    $audit = Audit::factory()->create([
        'project_id' => $project->id,
        'projectdataset_id' => $dataset->id,
        'performed_by' => $this->user->id,
        'status' => AuditStatus::Pending,
    ]);

    $result = $this->service->runAudit($dataset, $audit);
    $stats = $result->network_statistics;

    expect($stats)->toHaveKey('detailed');
    expect($stats)->toHaveKey('total_fibers');
    expect($stats)->toHaveKey('used_fibers');
    expect($stats)->toHaveKey('spare_fibers');
    expect($stats)->toHaveKey('occupation_rate');

    $detailed = $stats['detailed'];

    expect($detailed['cables']['total_count'])->toBe(3);
    expect($detailed['cables']['total_length_m'])->toBe(400.8);

    expect($detailed['fibers']['total_capacity'])->toBe(336);
    expect($detailed['fibers']['total_used'])->toBe(132);
    expect($detailed['fibers']['spare_fibers'])->toBe(204);

    expect($detailed['pathways']['total_count'])->toBe(2);
    expect((float) $detailed['pathways']['total_length_m'])->toBe(800.0);

    expect($detailed['equipment']['sites']['total'])->toBe(1);
    expect($detailed['equipment']['sites']['by_type']['NRO']['count'])->toBe(1);
    expect($detailed['equipment']['optical_boxes']['total'])->toBe(2);
    expect($detailed['equipment']['optical_boxes']['total_cassettes'])->toBe(12);

    expect($detailed['supports']['technical_points']['total'])->toBe(2);
    expect($detailed['supports']['conduits']['by_statut'])->toBeArray();

    expect($detailed['logements']['logements']['total'])->toBe(354);
    expect($detailed['logements']['logements']['max_capacity'])->toBe(600);
    expect($detailed['logements']['connected'])->toBe(12);

    expect($detailed['cables']['by_reference'])->toBeArray();
    expect($detailed['cables']['by_reference'])->toHaveCount(2);

    $rf029 = collect($detailed['cables']['by_reference'])->firstWhere('rf_code', 'RF000000000029');
    expect($rf029['manufacturer'])->toBe('ACOME');
    expect($rf029['fiber_count'])->toBe(144);
    expect($rf029['count'])->toBe(2);
    expect((float) $rf029['carto_length_m'])->toBe(200.8);
    expect((float) $rf029['adjusted_length_m'])->toBe(round((120.5 * 1.20) + 10 + (80.3 * 1.20) + 10, 2));

    $rf047 = collect($detailed['cables']['by_reference'])->firstWhere('rf_code', 'RF000000000047');
    expect($rf047['manufacturer'])->toBe('ACOME');
    expect($rf047['fiber_count'])->toBe(48);
    expect($rf047['count'])->toBe(1);
    expect((float) $rf047['carto_length_m'])->toBe(200.0);
    expect((float) $rf047['adjusted_length_m'])->toBe(round((200.0 * 1.20) + 10, 2));
});
