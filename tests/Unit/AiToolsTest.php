<?php

use App\Ai\Tools\GetAnomalies;
use App\Ai\Tools\GetAuditScores;
use App\Ai\Tools\GetBoxReference;
use App\Ai\Tools\GetCableReference;
use App\Ai\Tools\GetMcdRules;
use App\Ai\Tools\GetNetworkStats;
use App\Models\Audit;
use App\Models\Project;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Tools\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->project = Project::factory()->create();
    $this->audit = Audit::factory()->for($this->project)->create([
        'quality_score' => 82.5,
        'connectivity_score' => 90.0,
        'coherence_score' => 75.0,
        'capacity_score' => 80.0,
        'extensibility_score' => 70.0,
        'network_statistics' => [
            'detailed' => [
                'anomalies' => [
                    ['type' => 'transport', 'severity' => 'critical', 'shp' => 't_cable', 'message' => 'Cable CB-001: length 3500m exceeds 3000m', 'solution' => 'Add splice point'],
                    ['type' => 'cable', 'severity' => 'warning', 'shp' => 't_cable', 'message' => 'Cable CB-002: cb_fo_util is empty', 'solution' => 'Populate cb_fo_util'],
                    ['type' => 'ebp', 'severity' => 'info', 'shp' => 't_ebp', 'message' => 'EBP BP-001: bp_avct is C', 'solution' => 'Check status'],
                ],
                'fibers' => ['total_capacity' => 1200, 'total_used' => 800, 'spare_fibers' => 400, 'occupation_rate' => 66.67],
                'cables' => ['total_count' => 15, 'total_length_m' => 45000],
                'pathways' => [
                    'total_count' => 200,
                    'total_length_m' => 35000,
                    'by_implantation_type' => ['aerial' => 120, 'underground' => 80],
                    'by_logical_type' => ['transport' => 150, 'distribution' => 50],
                ],
                'equipment' => [
                    'sites' => ['total' => 12, 'by_type' => ['chamber' => 10, 'pole' => 2]],
                    'optical_boxes' => ['total' => 50, 'total_cassettes' => 200],
                ],
                'addresses' => ['total' => 300, 'prises_habitation' => 250, 'prises_professionnelles' => 50],
                'logements' => ['logements' => ['total' => 280, 'max_capacity' => 350, 'occupation_rate' => 80.0]],
            ],
        ],
    ]);
});

it('GetAuditScores returns correct scores', function () {
    $tool = new GetAuditScores($this->audit);
    $result = json_decode($tool->handle(new Request([])), true);

    expect((float) $result['overall'])->toBe(82.5);
    expect((float) $result['connectivity'])->toBe(90.0);
    expect((float) $result['coherence'])->toBe(75.0);
    expect((float) $result['capacity'])->toBe(80.0);
    expect((float) $result['extensibility'])->toBe(70.0);
    expect($result['interpretation'])->toBe('Bon');
});

it('GetAnomalies returns all anomalies without filter', function () {
    $tool = new GetAnomalies($this->audit);
    $result = json_decode($tool->handle(new Request([])), true);

    expect($result['counts']['total'])->toBe(3);
    expect($result['counts']['critical'])->toBe(1);
    expect($result['counts']['warning'])->toBe(1);
    expect($result['counts']['info'])->toBe(1);
    expect($result['anomalies'])->toHaveCount(3);
});

it('GetAnomalies filters by type', function () {
    $tool = new GetAnomalies($this->audit);
    $result = json_decode($tool->handle(new Request(['type' => 'transport'])), true);

    expect($result['anomalies'])->toHaveCount(1);
    expect($result['anomalies'][0]['type'])->toBe('transport');
});

it('GetAnomalies filters by severity', function () {
    $tool = new GetAnomalies($this->audit);
    $result = json_decode($tool->handle(new Request(['severity' => 'critical'])), true);

    expect($result['anomalies'])->toHaveCount(1);
    expect($result['anomalies'][0]['severity'])->toBe('critical');
});

it('GetNetworkStats returns network data', function () {
    $tool = new GetNetworkStats($this->audit);
    $result = json_decode($tool->handle(new Request([])), true);

    expect($result['fibers']['total_capacity'])->toBe(1200);
    expect($result['cables']['total_count'])->toBe(15);
    expect($result['addresses']['total'])->toBe(300);
});

it('GetNetworkStats reads producer-shaped pathway/equipment/logements keys', function () {
    $tool = new GetNetworkStats($this->audit);
    $result = json_decode($tool->handle(new Request([])), true);

    expect($result['pathways']['by_implantation_type'])->toBe(['aerial' => 120, 'underground' => 80]);
    expect($result['equipment']['sites_total'])->toBe(12);
    expect($result['logements']['total'])->toBe(280);
});

it('GetMcdRules returns rules for table', function () {
    $service = new AuditService;
    $tool = new GetMcdRules($service);

    $result = json_decode($tool->handle(new Request(['table' => 't_cable', 'phase' => 'PRO'])), true);

    expect($result['table'])->toBe('t_cable');
    expect($result['phase'])->toBe('PRO');
    expect($result)->toHaveKey('required_fields');
    expect($result)->toHaveKey('all_fields');
});

it('GetMcdRules returns error for unknown table', function () {
    $service = new AuditService;
    $tool = new GetMcdRules($service);

    $result = json_decode($tool->handle(new Request(['table' => 'nonexistent'])), true);

    expect($result)->toHaveKey('error');
    expect($result)->toHaveKey('available_tables');
});

it('GetCableReference finds existing reference', function () {
    $service = new AuditService;
    $tool = new GetCableReference($service);

    $result = json_decode($tool->handle(new Request(['rf_code' => 'NONEXISTENT'])), true);

    expect($result)->toHaveKey('error');
    expect($result)->toHaveKey('available_codes');
});

it('GetBoxReference finds existing reference', function () {
    $service = new AuditService;
    $tool = new GetBoxReference($service);

    $result = json_decode($tool->handle(new Request(['rf_code' => 'NONEXISTENT'])), true);

    expect($result)->toHaveKey('error');
    expect($result)->toHaveKey('available_codes');
});

it('AuditService is resolved as a singleton', function () {
    expect(app(AuditService::class))->toBe(app(AuditService::class));
});

it('tools have descriptions', function () {
    $tools = [
        new GetAuditScores($this->audit),
        new GetAnomalies($this->audit),
        new GetNetworkStats($this->audit),
        new GetMcdRules(new AuditService),
        new GetCableReference(new AuditService),
        new GetBoxReference(new AuditService),
    ];

    foreach ($tools as $tool) {
        expect($tool->description())->toBeString();
        expect(strlen($tool->description()))->toBeGreaterThan(0);
    }
});
