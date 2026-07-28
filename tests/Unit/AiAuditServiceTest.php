<?php

use App\Ai\Agents\AuditAnalystAgent;
use App\Models\Audit;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Contracts\Agent;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->agent = app(AuditAnalystAgent::class);
});

it('summarizes anomalies by type and severity', function () {
    $anomalies = [
        ['type' => 'cable', 'severity' => 'critical', 'message' => 'fiber usage is not populated'],
        ['type' => 'cable', 'severity' => 'warning', 'message' => 'statut does not match phase'],
        ['type' => 'cable', 'severity' => 'info', 'message' => 'avct should be X in phase'],
        ['type' => 'ebp', 'severity' => 'warning', 'message' => 'statut does not match phase'],
        ['type' => 'ebp', 'severity' => 'info', 'message' => 'avct should be X in phase'],
        ['type' => 'fiber_no_feeder', 'severity' => 'warning', 'message' => 'PBO not reachable'],
    ];

    $result = $this->agent->summarizeAnomalies($anomalies);

    expect($result['total'])->toBe(6);
    expect($result['critical'])->toBe(1);
    expect($result['warning'])->toBe(3);
    expect($result['info'])->toBe(2);

    expect($result['by_type']['cable']['total'])->toBe(3);
    expect($result['by_type']['cable']['critical'])->toBe(1);
    expect($result['by_type']['cable']['warning'])->toBe(1);
    expect($result['by_type']['cable']['info'])->toBe(1);

    expect($result['by_type']['ebp']['total'])->toBe(2);
    expect($result['by_type']['fiber_no_feeder']['total'])->toBe(1);
});

it('returns empty summary for no anomalies', function () {
    $result = $this->agent->summarizeAnomalies([]);

    expect($result['total'])->toBe(0);
    expect($result['critical'])->toBe(0);
    expect($result['warning'])->toBe(0);
    expect($result['info'])->toBe(0);
    foreach ($result['by_type'] as $type => $counts) {
        expect($counts['total'])->toBe(0);
    }
});

it('parses valid JSON response', function () {
    $json = '{"summary":"test","quality":"good","observations":["obs1"],"risks":["risk1"],"recommendations":["rec1"]}';

    $result = $this->agent->parseResponse($json);

    expect($result['summary'])->toBe('test');
    expect($result['quality'])->toBe('good');
    expect($result['observations'])->toBe(['obs1']);
    expect($result['risks'])->toBe(['risk1']);
    expect($result['recommendations'])->toBe(['rec1']);
});

it('extracts JSON from surrounding text', function () {
    $text = "Voici l'analyse:\n{\"summary\":\"résumé\",\"quality\":\"moyen\",\"observations\":[],\"risks\":[],\"recommendations\":[\"rec1\"]}\nFin.";

    $result = $this->agent->parseResponse($text);

    expect($result['summary'])->toBe('résumé');
    expect($result['recommendations'])->toBe(['rec1']);
});

it('falls back gracefully on garbage response', function () {
    $result = $this->agent->parseResponse('Ceci nest pas du JSON du tout.');

    expect($result['summary'])->toContain('Ceci nest pas du JSON');
    expect($result['quality'])->toBe('Non évalué.');
    expect($result['recommendations'])->toBe([]);
});

it('falls back on empty response', function () {
    $result = $this->agent->parseResponse('');

    expect($result['quality'])->toBe('Non évalué.');
    expect($result['recommendations'])->toBe([]);
});

it('uses defaults for missing fields in partial JSON', function () {
    $json = '{"summary":"only summary"}';

    $result = $this->agent->parseResponse($json);

    expect($result['summary'])->toBe('only summary');
    expect($result['quality'])->toBe('Non évalué.');
    expect($result['observations'])->toBe([]);
    expect($result['recommendations'])->toBe([]);
});

it('builds a prompt with expected sections', function () {
    $project = Project::factory()->create(['name' => 'Test Project']);
    $audit = Audit::factory()->create([
        'project_id' => $project->id,
        'phase_at_audit' => 'APD',
        'quality_score' => 65,
        'connectivity_score' => 100,
        'coherence_score' => 47,
        'capacity_score' => 70,
        'extensibility_score' => 41,
        'network_statistics' => [
            'detailed' => [
                'anomalies' => [],
                'cables' => ['total_count' => 5],
                'fibers' => ['total_capacity' => 100, 'occupation_rate' => 25],
                'pathways' => ['total_count' => 10, 'total_length_m' => 5000, 'by_implantation_type' => []],
                'logements' => ['logements' => ['total' => 50, 'max_capacity' => 100, 'occupation_rate' => 50]],
                'addresses' => ['total' => 30, 'prises_habitation' => 25, 'prises_professionnelles' => 5],
                'equipment' => ['optical_boxes' => ['total' => 8]],
                'fibers_per_pbo' => ['pbo_count' => 5, 'total_fiber_utile' => 30, 'total_fiber_disponible' => 20, 'feeder_cables' => []],
            ],
        ],
    ]);

    $prompt = $this->agent->buildPrompt($audit);

    expect($prompt)->toContain('## PROJET');
    expect($prompt)->toContain('Test Project');
    expect($prompt)->toContain('## SCORES QUALITÉ');
    expect($prompt)->toContain('## RÉSEAU');
    expect($prompt)->toContain('## ANOMALIES');
    expect($prompt)->toContain('## MCD (Phase APD)');
    expect($prompt)->toContain('## INSTRUCTIONS');
    expect($prompt)->toContain('Répondez en français');
    expect($prompt)->toContain('"summary"');
    expect($prompt)->toContain('"recommendations"');
    expect($prompt)->toContain('Acceptable');
});

it('interprets scores correctly', function () {
    $ref = new ReflectionClass($this->agent);
    $method = $ref->getMethod('interpretation');

    expect($method->invoke($this->agent, 95))->toBe('Excellent');
    expect($method->invoke($this->agent, 80))->toBe('Bon');
    expect($method->invoke($this->agent, 65))->toBe('Acceptable');
    expect($method->invoke($this->agent, 30))->toBe('Non-conforme');
});

it('implements Agent interface', function () {
    expect($this->agent)->toBeInstanceOf(Agent::class);
});
