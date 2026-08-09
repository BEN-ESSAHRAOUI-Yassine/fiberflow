<?php

use App\Exports\AuditExport;
use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectDataset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->engineer = User::factory()->engineer()->create();
    $this->project = Project::factory()->create();
    $this->dataset = ProjectDataset::factory()->create(['project_id' => $this->project->id]);
    $this->audit = Audit::factory()->completed()->create([
        'project_id' => $this->project->id,
        'projectdataset_id' => $this->dataset->id,
        'performed_by' => $this->admin->id,
        'network_statistics' => [
            'detailed' => [
                'anomalies' => [
                    ['type' => 'cable', 'severity' => 'critical', 'message' => 'Missing fiber data'],
                ],
                'cables' => [
                    'total_count' => 5,
                    'total_length_m' => 1200.5,
                    'by_reference' => [
                        [
                            'designation' => 'FO 48',
                            'rf_code' => 'RF000000000029',
                            'manufacturer' => 'ACOME',
                            'fiber_count' => 48,
                            'count' => 3,
                            'carto_length_m' => 800.0,
                            'adjusted_length_m' => 970.0,
                        ],
                    ],
                ],
                'fibers' => [
                    'total_capacity' => 240,
                    'total_used' => 80,
                    'spare_fibers' => 160,
                    'occupation_rate' => 33.3,
                ],
                'fibers_per_pbo' => [
                    'pbo_count' => 10,
                    'total_fiber_utile' => 80,
                    'total_fiber_disponible' => 160,
                    'feeder_cables' => [
                        [
                            'cable_code' => 'CB001',
                            'capacity' => 48,
                            'total_utile' => 40,
                            'total_disponible' => 8,
                            'zones' => [
                                ['zp_code' => 'ZP001'],
                            ],
                        ],
                    ],
                ],
                'pathways' => [
                    'total_count' => 10,
                    'total_length_m' => 5000.0,
                ],
                'equipment' => [
                    'sites' => ['total' => 2, 'by_type' => ['NRO' => ['count' => 1], 'SRO' => ['count' => 1]]],
                    'optical_boxes' => [
                        'total' => 15,
                        'total_cassettes' => 45,
                        'by_reference' => [
                            [
                                'designation' => 'B144',
                                'rf_code' => 'RF000000000030',
                                'manufacturer' => 'HUAWEI',
                                'logical_type' => 'B144',
                                'statut' => 'actif',
                                'avancement' => 'projeté',
                                'count' => 10,
                                'cassettes' => 30,
                            ],
                        ],
                    ],
                ],
                'supports' => [
                    'organismes' => ['OP' => 'Orange', 'VT' => 'Vitaliti'],
                    'technical_points' => [
                        'total' => 20,
                        'by_statut' => [
                            'REC' => [
                                'by_owner' => [
                                    'OP' => ['A' => 5, 'C' => 3, 'F' => 2, 'I' => 1, 'Z' => 0],
                                ],
                            ],
                        ],
                    ],
                    'conduits' => [
                        'by_statut' => [
                            'REC' => [
                                'by_owner' => [
                                    'OP' => ['underground_length' => 1000.0, 'aerial_length' => 200.0, 'facade_other_length' => 50.0],
                                ],
                            ],
                        ],
                    ],
                ],
                'logements' => [
                    'logements' => [
                        'total' => 354,
                        'max_capacity' => 600,
                        'occupation_rate' => 59.0,
                    ],
                    'sro_zone_count' => 3,
                    'pbo_zone_count' => 10,
                    'connected' => 80,
                ],
                'addresses' => [
                    'total' => 200,
                    'prises_habitation' => 180,
                    'prises_professionnelles' => 20,
                    'by_type_immeuble' => ['I' => 50, 'P' => 150],
                ],
            ],
        ],
    ]);
});

describe('PDF export', function () {

    it('returns PDF response for admin', function () {
        $response = $this->actingAs($this->admin)
            ->get("/projects/{$this->project->id}/audits/{$this->audit->id}/pdf");

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    });

    it('returns 401 for guest accessing PDF', function () {
        $response = $this->get("/projects/{$this->project->id}/audits/{$this->audit->id}/pdf");

        $response->assertRedirect('/login');
    });

    it('returns 404 for non-existent audit PDF', function () {
        $response = $this->actingAs($this->admin)
            ->get("/projects/{$this->project->id}/audits/99999/pdf");

        $response->assertStatus(404);
    });
});

describe('Excel export', function () {

    it('returns Excel response for admin', function () {
        $response = $this->actingAs($this->admin)
            ->get("/projects/{$this->project->id}/audits/{$this->audit->id}/excel");

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    });

    it('returns 401 for guest accessing Excel', function () {
        $response = $this->get("/projects/{$this->project->id}/audits/{$this->audit->id}/excel");

        $response->assertRedirect('/login');
    });

    it('returns 404 for non-existent audit Excel', function () {
        $response = $this->actingAs($this->admin)
            ->get("/projects/{$this->project->id}/audits/99999/excel");

        $response->assertStatus(404);
    });

    it('writes styled, frozen, filterable headers on every sheet', function () {
        $path = 'styled-headers-test.xlsx';

        Excel::store(new AuditExport($this->audit), $path, 'local');

        $spreadsheet = (new Xlsx)->load(storage_path("app/private/{$path}"));

        $expected = [
            'Anomalies' => ['SHP', 'Sévérité', 'Type', 'Message', 'Solution'],
            'Câbles' => ['Désignation', 'RF Code', 'Fabricant', 'FO', 'Modulo', 'Installation', 'Nb', 'Carto (m)', 'Ajusté (m)'],
            'Fibre' => ['Câble', 'Capacité', 'Utile', 'Disponible', 'Nb Zones PBO', 'Zones'],
            'Boîtes optiques' => ['Désignation', 'RF Code', 'Fabricant', 'Type Logique', 'Statut', 'Avancement', 'Nb', 'Cassettes'],
            'Supports' => ['Statut', 'Propriétaire', 'Appui', 'Chambre', 'Façade', 'Immeuble', 'Autre', 'Total'],
            'Conduites' => ['Statut', 'Propriétaire', 'Souterrain (m)', 'Aérien (m)', 'Façade/Autre (m)', 'Total (m)'],
            'Logements' => ['Section', 'Total', 'Capacité Max', 'Connecté', 'Occupation', 'Zones SRO', 'Zones PBO'],
        ];

        foreach ($expected as $sheetName => $headings) {
            $sheet = $spreadsheet->getSheetByName($sheetName);

            expect($sheet)->not->toBeNull();
            expect(array_slice($sheet->rangeToArray('A1:J1')[0], 0, count($headings)))->toBe($headings);
            expect($sheet->getStyle('A1')->getFill()->getFillType())->toBe('solid');
            expect($sheet->getStyle('A1')->getFill()->getStartColor()->getRGB())->toBe('2563EB');
            expect($sheet->getFreezePane())->toBe('A2');
            expect($sheet->getAutoFilter()->getRange())->toBe('A1:'.$sheet->getHighestColumn(1).'1');
        }

        @unlink(storage_path("app/private/{$path}"));
    });
});

describe('Report access control', function () {

    it('allows engineer to download PDF for own audit', function () {
        $audit = Audit::factory()->completed()->create([
            'project_id' => $this->project->id,
            'performed_by' => $this->engineer->id,
        ]);

        $response = $this->actingAs($this->engineer)
            ->get("/projects/{$this->project->id}/audits/{$audit->id}/pdf");

        $response->assertStatus(200);
    });

    it('denies engineer to download PDF for another engineers audit', function () {
        $response = $this->actingAs($this->engineer)
            ->get("/projects/{$this->project->id}/audits/{$this->audit->id}/pdf");

        $response->assertForbidden();
    });

    it('allows engineer to download Excel for own audit', function () {
        $audit = Audit::factory()->completed()->create([
            'project_id' => $this->project->id,
            'performed_by' => $this->engineer->id,
        ]);

        $response = $this->actingAs($this->engineer)
            ->get("/projects/{$this->project->id}/audits/{$audit->id}/excel");

        $response->assertStatus(200);
    });

    it('denies engineer to download Excel for another engineers audit', function () {
        $response = $this->actingAs($this->engineer)
            ->get("/projects/{$this->project->id}/audits/{$this->audit->id}/excel");

        $response->assertForbidden();
    });
});
