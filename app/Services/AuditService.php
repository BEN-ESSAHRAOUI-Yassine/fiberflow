<?php

namespace App\Services;

use App\Enums\AuditStatus;
use App\Models\Audit;
use App\Models\ProjectDataset;

class AuditService
{
    private ?array $cableReferences = null;

    private ?array $boxReferences = null;

    private ?array $organismes = null;

    private ?array $mcdRules = null;

    private function loadOrganismes(): array
    {
        if ($this->organismes !== null) {
            return $this->organismes;
        }

        $path = base_path('docs/t_organisme.tsv');
        if (! file_exists($path)) {
            $this->organismes = [];

            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $orgs = [];
        foreach ($lines as $i => $line) {
            if ($i === 0) {
                continue;
            }
            $cols = str_getcsv($line, "\t");
            $code = $cols[0] ?? '';
            $nom = $cols[2] ?? '';
            if ($code !== '') {
                $orgs[$code] = $nom;
            }
        }

        $this->organismes = $orgs;

        return $orgs;
    }

    private function loadMcdRules(): array
    {
        if ($this->mcdRules !== null) {
            return $this->mcdRules;
        }

        $path = base_path('docs/MCD_Attributs.tsv');
        if (! file_exists($path)) {
            $this->mcdRules = [];

            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $rules = [];
        foreach ($lines as $i => $line) {
            if ($i === 0) {
                continue;
            }
            $cols = str_getcsv($line, "\t");
            if (count($cols) < 11) {
                continue;
            }
            $table = trim($cols[0]);
            $field = trim($cols[1]);
            if ($table === '' || $field === '') {
                continue;
            }
            $rules[$table][$field] = [
                'PRO' => trim($cols[6] ?? ''),
                'EXE_DISTRI' => trim($cols[7] ?? ''),
                'EXE_TRANSP' => trim($cols[8] ?? ''),
                'REC_TRANSP' => trim($cols[9] ?? ''),
                'REC_DISTRI' => trim($cols[10] ?? ''),
            ];
        }

        $this->mcdRules = $rules;

        return $rules;
    }

    private function getRequiredFields(array $tableRules, string $phase): array
    {
        $columns = match ($phase) {
            'PRO' => ['PRO'],
            'EXE' => ['EXE_DISTRI', 'EXE_TRANSP'],
            'REC' => ['REC_TRANSP', 'REC_DISTRI'],
            default => ['PRO'],
        };

        $required = [];
        foreach ($tableRules as $field => $reqs) {
            foreach ($columns as $col) {
                if (($reqs[$col] ?? '') === 'O') {
                    $required[] = $field;

                    break;
                }
            }
        }

        return $required;
    }

    private function loadCableReferences(): array
    {
        if ($this->cableReferences !== null) {
            return $this->cableReferences;
        }

        $path = base_path('docs/t_reference materials.tsv');
        if (! file_exists($path)) {
            $this->cableReferences = [];

            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $orgs = $this->loadOrganismes();
        $refs = [];

        foreach ($lines as $i => $line) {
            if ($i === 0) {
                continue;
            }
            $cols = str_getcsv($line, "\t");
            $rfCode = $cols[0] ?? '';
            $rfType = $cols[1] ?? '';
            $rfFabric = $cols[2] ?? '';
            $rfDesign = $cols[3] ?? '';
            $rfEtat = $cols[4] ?? '';
            $rfComment = $cols[5] ?? '';

            if ($rfCode === '' || $rfType !== 'CA') {
                continue;
            }

            $parsed = $this->parseCableDesign($rfDesign);
            $manufacturer = $orgs[$rfFabric] ?? ($rfFabric ?: 'Unknown');

            $refs[$rfCode] = [
                'rf_code' => $rfCode,
                'manufacturer' => $manufacturer,
                'designation' => $rfDesign,
                'description' => $rfComment,
                'fiber_count' => $parsed['fiber_count'],
                'modulo' => $parsed['modulo'],
                'installation' => $parsed['installation'],
            ];
        }

        $this->cableReferences = $refs;

        return $refs;
    }

    private function loadBoxReferences(): array
    {
        if ($this->boxReferences !== null) {
            return $this->boxReferences;
        }

        $path = base_path('docs/t_reference materials.tsv');
        if (! file_exists($path)) {
            $this->boxReferences = [];

            return [];
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $orgs = $this->loadOrganismes();
        $refs = [];

        foreach ($lines as $i => $line) {
            if ($i === 0) {
                continue;
            }
            $cols = str_getcsv($line, "\t");
            $rfCode = $cols[0] ?? '';
            $rfType = $cols[1] ?? '';
            $rfFabric = $cols[2] ?? '';
            $rfDesign = $cols[3] ?? '';
            $rfEtat = $cols[4] ?? '';
            $rfComment = $cols[5] ?? '';

            if ($rfCode === '' || $rfType !== 'BP') {
                continue;
            }

            $manufacturer = $orgs[$rfFabric] ?? ($rfFabric ?: 'Unknown');

            $refs[$rfCode] = [
                'rf_code' => $rfCode,
                'manufacturer' => $manufacturer,
                'designation' => $rfDesign,
                'description' => $rfComment,
            ];
        }

        $this->boxReferences = $refs;

        return $refs;
    }

    private function parseCableDesign(string $design): array
    {
        $fiberCount = 0;
        $modulo = 0;
        $installation = '';

        if (preg_match('/(\d+)\s*FO/i', $design, $m)) {
            $fiberCount = (int) $m[1];
        }

        if (preg_match('/modulo\s*(\d+)/i', $design, $m)) {
            $modulo = (int) $m[1];
        }

        if (preg_match('/\b(sout(?:errain)?|mixte|imb|a[ée]rien(?:ne)?)\b/i', $design, $m)) {
            $raw = strtolower($m[1]);
            $installation = match (true) {
                str_starts_with($raw, 'sout') => 'sout',
                $raw === 'mixte' => 'mixte',
                $raw === 'imb' => 'imb',
                str_starts_with($raw, 'a') => 'aerien',
                default => $raw,
            };
        }

        return [
            'fiber_count' => $fiberCount,
            'modulo' => $modulo,
            'installation' => $installation,
        ];
    }

    public function runAudit(ProjectDataset $dataset, Audit $audit): Audit
    {
        $geojson = $dataset->geojson;
        $project = $dataset->project;

        $phase = $project->study_phase;

        $cableReferences = $this->loadCableReferences();
        $boxReferences = $this->loadBoxReferences();
        $mcdRules = $this->loadMcdRules();

        $transportAnomalies = $this->auditTransport($geojson);

        $distributionAnomalies = $this->auditDistribution($geojson);

        $cableAnomalies = $this->auditCables($geojson, $cableReferences, $phase->value, $mcdRules);

        $nodeCodes = [];
        foreach ($geojson['t_noeud'] ?? [] as $node) {
            $nodeCodes[] = $node['properties']['nd_code'] ?? '';
        }
        $nodeCodes = array_filter(array_unique($nodeCodes));

        $ebpAnomalies = $this->auditEBP($geojson, $boxReferences, $phase->value, $nodeCodes, $mcdRules);

        $networkStats = $this->extractDetailedStatistics($geojson);

        $fiberPboResult = $this->calculateFiberPerPBO($geojson);
        $networkStats['detailed']['fibers_per_pbo'] = $fiberPboResult['stats'];

        $allAnomalies = array_merge($transportAnomalies, $distributionAnomalies, $cableAnomalies, $ebpAnomalies, $fiberPboResult['anomalies']);
        $networkStats['detailed']['anomalies'] = $allAnomalies;
        $anomalyCount = 0;
        $criticalCount = 0;

        foreach ($allAnomalies as $anomaly) {
            $anomalyCount++;
            if (($anomaly['severity'] ?? 'warning') === 'critical') {
                $criticalCount++;
            }
        }

        $scores = $this->calculateScores($allAnomalies, $networkStats, $geojson);

        $audit->update([
            'status' => AuditStatus::Completed,
            'quality_score' => $scores['overall'],
            'connectivity_score' => $scores['connectivity'],
            'coherence_score' => $scores['coherence'],
            'capacity_score' => $scores['capacity'],
            'extensibility_score' => $scores['extensibility'],
            'network_statistics' => $networkStats,
            'anomaly_count' => $anomalyCount,
            'critical_anomaly_count' => $criticalCount,
            'error_message' => null,
            'completed_at' => now(),
        ]);

        return $audit->fresh();
    }

    private function auditTransport(array $geojson): array
    {
        $anomalies = [];
        $nroZones = $geojson['t_znro'] ?? [];
        $sroZones = $geojson['t_zsro'] ?? [];
        $cables = $geojson['t_cable'] ?? [];

        if (! empty($nroZones) && empty($sroZones)) {
            $anomalies[] = [
                'type' => 'transport',
                'severity' => 'critical',
                'shp' => 't_znro / t_zsro',
                'message' => 'NRO zones exist but no SRO zones found',
                'solution' => 'Ajouter une zone SRO (t_zsro) ou vérifier les couches du projet',
            ];
        }

        $totalFibers = 0;
        $usedFibers = 0;
        foreach ($cables as $cable) {
            $props = $cable['properties'] ?? [];
            $totalFibers += (int) ($props['cb_capafo'] ?? $props['nb_fibres'] ?? 0);
            $usedFibers += (int) ($props['cb_fo_util'] ?? $props['nb_fibres_utilisees'] ?? 0);

            $length = (float) ($props['cb_lgreel'] ?? 0);
            if ($length > 3000) {
                $cbCode = $props['cb_code'] ?? 'Unknown';
                $anomalies[] = [
                    'type' => 'transport',
                    'severity' => 'critical',
                    'shp' => 't_cable',
                    'message' => "Cable {$cbCode}: length {$length}m exceeds 3000m limit — épissurage required",
                    'solution' => 'Prévoir un point d\'épissurage dans t_ebp et découper le câble en tronçons < 3000 m',
                ];
            }
        }

        if ($totalFibers > 0) {
            $occupationRate = ($usedFibers / $totalFibers) * 100;
            if ($occupationRate >= 95) {
                $anomalies[] = [
                    'type' => 'transport',
                    'severity' => 'critical',
                    'shp' => 't_cable',
                    'message' => "Backbone fiber saturation critical: {$occupationRate}%",
                    'solution' => 'Augmenter la capacité du câble ou ajouter un nouveau câble de transport',
                ];
            } elseif ($occupationRate >= 80) {
                $anomalies[] = [
                    'type' => 'transport',
                    'severity' => 'warning',
                    'shp' => 't_cable',
                    'message' => "Backbone fiber saturation warning: {$occupationRate}%",
                    'solution' => 'Prévoir une augmentation de capacité à court terme',
                ];
            }
        }

        return $anomalies;
    }

    private function auditDistribution(array $geojson): array
    {
        $anomalies = [];
        $nodes = $geojson['t_noeud'] ?? [];

        $sroNodes = [];
        $boNodes = [];
        $pboNodes = [];

        foreach ($nodes as $node) {
            $props = $node['properties'] ?? [];
            $nodeType = $props['type'] ?? $props['nature'] ?? '';
            $nodeTypeStr = (string) $nodeType;

            if (str_contains($nodeTypeStr, 'SRO')) {
                $sroNodes[] = $props;
            } elseif (str_contains($nodeTypeStr, 'PBO')) {
                $pboNodes[] = $props;
            } elseif (str_contains($nodeTypeStr, 'BO')) {
                $boNodes[] = $props;
            }
        }

        $pboWithParent = 0;
        foreach ($pboNodes as $pbo) {
            if (! empty($pbo['id_bo']) || ! empty($pbo['parent_id'])) {
                $pboWithParent++;
            }
        }

        $orphanPboCount = count($pboNodes) - $pboWithParent;
        if ($orphanPboCount > 0) {
            $anomalies[] = [
                'type' => 'distribution',
                'severity' => 'critical',
                'shp' => 't_noeud (PBO) / t_bo',
                'message' => "{$orphanPboCount} orphan PBO(s) without BO connection",
                'solution' => 'Raccorder chaque PBO à un BO (id_bo ou parent_id dans t_noeud)',
            ];
        }

        $boWithParent = 0;
        foreach ($boNodes as $bo) {
            if (! empty($bo['id_sro']) || ! empty($bo['parent_id'])) {
                $boWithParent++;
            }
        }

        $orphanBoCount = count($boNodes) - $boWithParent;
        if ($orphanBoCount > 0) {
            $anomalies[] = [
                'type' => 'distribution',
                'severity' => 'critical',
                'shp' => 't_noeud (BO) / t_zsro',
                'message' => "{$orphanBoCount} BO(s) without parent SRO",
                'solution' => 'Associer chaque BO à une zone SRO (id_sro ou parent_id dans t_noeud)',
            ];
        }

        if (! empty($sroNodes) && empty($boNodes)) {
            $anomalies[] = [
                'type' => 'distribution',
                'severity' => 'warning',
                'shp' => 't_zsro / t_noeud (BO)',
                'message' => 'SRO nodes exist but no BO nodes found',
                'solution' => 'Créer des BO sous la zone SRO dans t_noeud',
            ];
        }

        return $anomalies;
    }

    private function auditCables(array $geojson, array $cableReferences, string $phase, array $mcdRules): array
    {
        $anomalies = [];
        $cables = $geojson['t_cable'] ?? [];

        $nodeCodes = [];
        foreach ($geojson['t_noeud'] ?? [] as $node) {
            $props = $node['properties'] ?? [];
            $nodeCodes[] = $props['nd_code'] ?? '';
        }

        $orgCodes = [];
        foreach ($geojson['t_organisme'] ?? [] as $org) {
            $props = $org['properties'] ?? [];
            $orgCodes[] = $props['or_code'] ?? '';
        }

        $nodeCodes = array_filter(array_unique($nodeCodes));
        $orgCodes = array_filter(array_unique($orgCodes));

        $requiredFields = $this->getRequiredFields($mcdRules['t_cable'] ?? [], $phase);

        foreach ($cables as $cable) {
            $props = $cable['properties'] ?? [];
            $cbCode = $props['cb_code'] ?? 'Unknown';
            $rfCode = $props['cb_rf_code'] ?? null;
            $refData = $rfCode && isset($cableReferences[$rfCode]) ? $cableReferences[$rfCode] : null;

            // 0. Required field check
            foreach ($requiredFields as $field) {
                if (! isset($props[$field]) || $props[$field] === '' || $props[$field] === null) {
                    $anomalies[] = [
                        'type' => 'cable',
                        'severity' => 'critical',
                        'shp' => 't_cable',
                        'message' => "Cable {$cbCode}: required field '{$field}' is empty",
                        'solution' => "Renseigner la valeur du champ {$field} dans t_cable",
                    ];
                }
            }

            // 1. Modulo consistency: cb_modulo vs reference modulo
            if ($refData && isset($props['cb_modulo']) && $props['cb_modulo'] !== '' && $props['cb_modulo'] !== null) {
                $actualModulo = (int) $props['cb_modulo'];
                $refModulo = (int) ($refData['modulo'] ?? 0);
                if ($refModulo > 0 && $actualModulo !== $refModulo) {
                    $anomalies[] = [
                        'type' => 'cable',
                        'severity' => 'warning',
                        'shp' => 't_cable / t_rf_cable',
                        'message' => "Cable {$cbCode}: cb_modulo={$actualModulo} differs from reference {$rfCode} modulo={$refModulo}",
                        'solution' => 'Corriger cb_modulo dans t_cable ou le modulo dans t_rf_cable pour les harmoniser',
                    ];
                }
            }

            // 2. Capacity vs reference: cb_capafo vs reference fiber_count
            if ($refData && isset($props['cb_capafo']) && $props['cb_capafo'] !== '' && $props['cb_capafo'] !== null) {
                $actualCap = (int) $props['cb_capafo'];
                $refCap = (int) ($refData['fiber_count'] ?? 0);
                if ($refCap > 0 && $actualCap !== $refCap) {
                    $anomalies[] = [
                        'type' => 'cable',
                        'severity' => 'warning',
                        'shp' => 't_cable / t_rf_cable',
                        'message' => "Cable {$cbCode}: cb_capafo={$actualCap} differs from reference {$rfCode} fiber_count={$refCap}",
                        'solution' => 'Corriger cb_capafo dans t_cable ou le nombre de fibres dans t_rf_cable pour les harmoniser',
                    ];
                }
            }

            // 3. Statut phase check
            if (isset($props['cb_statut']) && $props['cb_statut'] !== '') {
                $statut = strtoupper($props['cb_statut']);
                $expectedStatut = match ($phase) {
                    'APS' => 'APS',
                    'APD' => 'APD',
                    'PRO' => 'PRO',
                    'EXE' => 'EXE',
                    'REC' => 'REC',
                    default => null,
                };
                if ($expectedStatut && $statut !== $expectedStatut) {
                    $anomalies[] = [
                        'type' => 'cable',
                        'severity' => 'warning',
                        'shp' => 't_cable',
                        'message' => "Cable {$cbCode}: cb_statut='{$statut}' does not match project phase '{$phase}'",
                        'solution' => "Corriger cb_statut dans t_cable pour correspondre à la phase '{$phase}' du projet",
                    ];
                }
            }

            // 4. Orphan node endpoints
            $nd1 = $props['cb_nd1'] ?? null;
            $nd2 = $props['cb_nd2'] ?? null;
            if ($nd1 && ! empty($nodeCodes) && ! in_array($nd1, $nodeCodes)) {
                $anomalies[] = [
                    'type' => 'cable',
                    'severity' => 'critical',
                    'shp' => 't_cable / t_noeud',
                    'message' => "Cable {$cbCode}: endpoint cb_nd1='{$nd1}' not found in t_noeud",
                    'solution' => "Ajouter le nœud {$nd1} dans t_noeud ou corriger cb_nd1 dans t_cable",
                ];
            }
            if ($nd2 && ! empty($nodeCodes) && ! in_array($nd2, $nodeCodes)) {
                $anomalies[] = [
                    'type' => 'cable',
                    'severity' => 'critical',
                    'shp' => 't_cable / t_noeud',
                    'message' => "Cable {$cbCode}: endpoint cb_nd2='{$nd2}' not found in t_noeud",
                    'solution' => "Ajouter le nœud {$nd2} dans t_noeud ou corriger cb_nd2 dans t_cable",
                ];
            }

            // 5. Avancement check
            if (isset($props['cb_avct']) && $props['cb_avct'] !== '') {
                $avct = strtoupper($props['cb_avct']);
                if ($phase === 'REC') {
                    if (! in_array($avct, ['E', 'S'])) {
                        $anomalies[] = [
                            'type' => 'cable',
                            'severity' => 'warning',
                            'shp' => 't_cable',
                            'message' => "Cable {$cbCode}: cb_avct='{$avct}' should be 'E' (EXISTANT) or 'S' (EN SERVICE) in REC phase",
                            'solution' => "Corriger cb_avct dans t_cable : 'E' pour EXISTANT ou 'S' pour EN SERVICE",
                        ];
                    }
                } elseif (in_array($phase, ['APS', 'APD', 'PRO', 'EXE'])) {
                    if (! in_array($avct, ['C', 'T'])) {
                        $anomalies[] = [
                            'type' => 'cable',
                            'severity' => 'info',
                            'shp' => 't_cable',
                            'message' => "Cable {$cbCode}: cb_avct='{$avct}' should be 'C' (A CREER) or 'T' (TRAVAUX) in {$phase} phase",
                            'solution' => "Corriger cb_avct dans t_cable : 'C' pour A CREER ou 'T' pour TRAVAUX",
                        ];
                    }
                }
            }

            // 6. Fiber usage check
            $foUtil = $props['cb_fo_util'] ?? null;
            if ($foUtil === null || $foUtil === '' || (int) $foUtil === 0) {
                $anomalies[] = [
                    'type' => 'cable',
                    'severity' => 'critical',
                    'shp' => 't_cable',
                    'message' => "Cable {$cbCode}: fiber usage (cb_fo_util) is not populated",
                    'solution' => 'Renseigner cb_fo_util (nombre de fibres utilisées) dans t_cable',
                ];
            }

            // 7. Owner/manager reference check
            $prop = $props['cb_prop'] ?? null;
            $gest = $props['cb_gest'] ?? null;
            if ($prop && ! empty($orgCodes) && ! in_array($prop, $orgCodes)) {
                $anomalies[] = [
                    'type' => 'cable',
                    'severity' => 'warning',
                    'shp' => 't_cable / t_organisme',
                    'message' => "Cable {$cbCode}: cb_prop='{$prop}' not found in t_organisme",
                    'solution' => "Ajouter l'organisme {$prop} dans t_organisme ou corriger cb_prop dans t_cable",
                ];
            }
            if ($gest && ! empty($orgCodes) && ! in_array($gest, $orgCodes)) {
                $anomalies[] = [
                    'type' => 'cable',
                    'severity' => 'warning',
                    'shp' => 't_cable / t_organisme',
                    'message' => "Cable {$cbCode}: cb_gest='{$gest}' not found in t_organisme",
                    'solution' => "Ajouter l'organisme {$gest} dans t_organisme ou corriger cb_gest dans t_cable",
                ];
            }
        }

        return $anomalies;
    }

    private function auditEBP(array $geojson, array $boxReferences, string $phase, array $nodeCodes, array $mcdRules): array
    {
        $anomalies = [];
        $ebpItems = $geojson['t_ebp'] ?? [];

        $orgCodes = [];
        foreach ($geojson['t_organisme'] ?? [] as $org) {
            $props = $org['properties'] ?? [];
            $orgCodes[] = $props['or_code'] ?? '';
        }
        $orgCodes = array_filter(array_unique($orgCodes));

        $ptechCodes = [];
        foreach ($geojson['t_ptech'] ?? [] as $pt) {
            $props = $pt['properties'] ?? [];
            $ptechCodes[] = $props['pt_code'] ?? '';
        }
        $ptechCodes = array_filter(array_unique($ptechCodes));

        $requiredFields = $this->getRequiredFields($mcdRules['t_ebp'] ?? [], $phase);

        foreach ($ebpItems as $ebp) {
            $props = $ebp['properties'] ?? [];
            $bpCode = $props['bp_code'] ?? 'Unknown';
            $rfCode = $props['bp_rf_code'] ?? null;
            $refData = $rfCode && isset($boxReferences[$rfCode]) ? $boxReferences[$rfCode] : null;

            // 0. Required field check
            foreach ($requiredFields as $field) {
                if (! isset($props[$field]) || $props[$field] === '' || $props[$field] === null) {
                    $anomalies[] = [
                        'type' => 'ebp',
                        'severity' => 'critical',
                        'shp' => 't_ebp',
                        'message' => "EBP {$bpCode}: required field '{$field}' is empty",
                        'solution' => "Renseigner la valeur du champ {$field} dans t_ebp",
                    ];
                }
            }

            // 1. Reference check: bp_rf_code → t_reference (BP type)
            if ($rfCode && ! isset($boxReferences[$rfCode])) {
                $anomalies[] = [
                    'type' => 'ebp',
                    'severity' => 'warning',
                    'shp' => 't_ebp / t_rf_boite',
                    'message' => "EBP {$bpCode}: bp_rf_code='{$rfCode}' not found in reference materials",
                    'solution' => "Ajouter la référence {$rfCode} dans t_rf_boite ou corriger bp_rf_code dans t_ebp",
                ];
            }

            // 2. Statut phase check
            if (isset($props['bp_statut']) && $props['bp_statut'] !== '') {
                $statut = strtoupper($props['bp_statut']);
                $expectedStatut = match ($phase) {
                    'APS' => 'APS',
                    'APD' => 'APD',
                    'PRO' => 'PRO',
                    'EXE' => 'EXE',
                    'REC' => 'REC',
                    default => null,
                };
                if ($expectedStatut && $statut !== $expectedStatut) {
                    $anomalies[] = [
                        'type' => 'ebp',
                        'severity' => 'warning',
                        'shp' => 't_ebp',
                        'message' => "EBP {$bpCode}: bp_statut='{$statut}' does not match project phase '{$phase}'",
                        'solution' => "Corriger bp_statut dans t_ebp pour correspondre à la phase '{$phase}' du projet",
                    ];
                }
            }

            // 3. Avancement check
            if (isset($props['bp_avct']) && $props['bp_avct'] !== '') {
                $avct = strtoupper($props['bp_avct']);
                if ($phase === 'REC') {
                    if (! in_array($avct, ['E', 'S'])) {
                        $anomalies[] = [
                            'type' => 'ebp',
                            'severity' => 'warning',
                            'shp' => 't_ebp',
                            'message' => "EBP {$bpCode}: bp_avct='{$avct}' should be 'E' (EXISTANT) or 'S' (EN SERVICE) in REC phase",
                            'solution' => "Corriger bp_avct dans t_ebp : 'E' pour EXISTANT ou 'S' pour EN SERVICE",
                        ];
                    }
                } elseif (in_array($phase, ['APS', 'APD', 'PRO', 'EXE'])) {
                    if (! in_array($avct, ['C', 'T'])) {
                        $anomalies[] = [
                            'type' => 'ebp',
                            'severity' => 'info',
                            'shp' => 't_ebp',
                            'message' => "EBP {$bpCode}: bp_avct='{$avct}' should be 'C' (A CREER) or 'T' (TRAVAUX) in {$phase} phase",
                            'solution' => "Corriger bp_avct dans t_ebp : 'C' pour A CREER ou 'T' pour TRAVAUX",
                        ];
                    }
                }
            }

            // 4. Orphan technical point reference
            $ptCode = $props['bp_pt_code'] ?? null;
            if ($ptCode && ! empty($ptechCodes) && ! in_array($ptCode, $ptechCodes)) {
                $anomalies[] = [
                    'type' => 'ebp',
                    'severity' => 'critical',
                    'shp' => 't_ebp / t_ptech',
                    'message' => "EBP {$bpCode}: bp_pt_code='{$ptCode}' not found in t_ptech",
                    'solution' => "Ajouter le point technique {$ptCode} dans t_ptech ou corriger bp_pt_code dans t_ebp",
                ];
            }

            // 5. Owner/manager reference check
            $prop = $props['bp_prop'] ?? null;
            $gest = $props['bp_gest'] ?? null;
            if ($prop && ! empty($orgCodes) && ! in_array($prop, $orgCodes)) {
                $anomalies[] = [
                    'type' => 'ebp',
                    'severity' => 'warning',
                    'shp' => 't_ebp / t_organisme',
                    'message' => "EBP {$bpCode}: bp_prop='{$prop}' not found in t_organisme",
                    'solution' => "Ajouter l'organisme {$prop} dans t_organisme ou corriger bp_prop dans t_ebp",
                ];
            }
            if ($gest && ! empty($orgCodes) && ! in_array($gest, $orgCodes)) {
                $anomalies[] = [
                    'type' => 'ebp',
                    'severity' => 'warning',
                    'shp' => 't_ebp / t_organisme',
                    'message' => "EBP {$bpCode}: bp_gest='{$gest}' not found in t_organisme",
                    'solution' => "Ajouter l'organisme {$gest} dans t_organisme ou corriger bp_gest dans t_ebp",
                ];
            }
        }

        return $anomalies;
    }

    private function extractDetailedStatistics(array $geojson): array
    {
        $stats = [];

        foreach ($geojson as $table => $features) {
            $stats[$table] = count($features);
        }

        $cables = $geojson['t_cable'] ?? [];
        $totalFibers = 0;
        $usedFibers = 0;
        $cableTotalLength = 0.0;
        $cablesByReference = [];
        $references = $this->loadCableReferences();

        foreach ($cables as $cable) {
            $props = $cable['properties'] ?? [];
            $capafo = (int) ($props['cb_capafo'] ?? 0);
            $foUtil = (int) ($props['cb_fo_util'] ?? 0);
            $length = (float) ($props['cb_lgreel'] ?? 0);
            $rfCode = $props['cb_rf_code'] ?? null;
            $cableStatut = $props['cb_statut'] ?? null;
            $cableAvct = $props['cb_avct'] ?? null;

            $totalFibers += $capafo;
            $usedFibers += $foUtil;
            $cableTotalLength += $length;

            $adjustedLength = ($length * 1.20) + 10;

            $refKey = $rfCode && isset($references[$rfCode]) ? $rfCode : ($rfCode ?: '__unknown');
            $refData = $rfCode && isset($references[$rfCode]) ? $references[$rfCode] : null;

            $designation = $refData['designation'] ?? ($rfCode ?: 'Unknown');
            $description = $refData['description'] ?? '';
            $manufacturer = $refData['manufacturer'] ?? 'Unknown';
            $fiberCount = $refData['fiber_count'] ?? 0;
            $modulo = $refData['modulo'] ?? '';
            $installation = $refData['installation'] ?? '';

            if (! isset($cablesByReference[$refKey])) {
                $cablesByReference[$refKey] = [
                    'rf_code' => $rfCode ?: 'N/A',
                    'designation' => $designation,
                    'description' => $description,
                    'manufacturer' => $manufacturer,
                    'fiber_count' => $fiberCount,
                    'modulo' => $modulo,
                    'installation' => $installation,
                    'count' => 0,
                    'fibers' => 0,
                    'carto_length_m' => 0.0,
                    'adjusted_length_m' => 0.0,
                    'statut' => null,
                    'avancement' => null,
                ];
            }
            $cablesByReference[$refKey]['count']++;
            $cablesByReference[$refKey]['fibers'] += $capafo;
            $cablesByReference[$refKey]['carto_length_m'] += $length;
            $cablesByReference[$refKey]['adjusted_length_m'] += $adjustedLength;
            if ($cableStatut !== null && $cableStatut !== '') {
                $cablesByReference[$refKey]['statut'] = $cableStatut;
            }
            if ($cableAvct !== null && $cableAvct !== '') {
                $cablesByReference[$refKey]['avancement'] = $cableAvct;
            }
        }

        $spareFibers = $totalFibers - $usedFibers;
        $stats['total_fibers'] = $totalFibers;
        $stats['used_fibers'] = $usedFibers;
        $stats['spare_fibers'] = max(0, $spareFibers);
        $stats['occupation_rate'] = $totalFibers > 0 ? round(($usedFibers / $totalFibers) * 100, 2) : 0;

        $pathways = $geojson['t_cheminement'] ?? [];
        $pathwayTotalLength = 0.0;
        $pathwaysByImpType = [];
        $pathwaysByLogType = [];

        foreach ($pathways as $path) {
            $props = $path['properties'] ?? [];
            $length = (float) ($props['cm_long'] ?? 0);
            $impType = $props['cm_typ_imp'] ?? 'Unknown';
            $logType = $props['cm_typelog'] ?? 'Unknown';

            $pathwayTotalLength += $length;

            if (! isset($pathwaysByImpType[$impType])) {
                $pathwaysByImpType[$impType] = ['count' => 0, 'length_m' => 0.0];
            }
            $pathwaysByImpType[$impType]['count']++;
            $pathwaysByImpType[$impType]['length_m'] += $length;

            if (! isset($pathwaysByLogType[$logType])) {
                $pathwaysByLogType[$logType] = ['count' => 0, 'length_m' => 0.0];
            }
            $pathwaysByLogType[$logType]['count']++;
            $pathwaysByLogType[$logType]['length_m'] += $length;
        }

        $ebpItems = $geojson['t_ebp'] ?? [];
        $sites = $geojson['t_sitetech'] ?? [];
        $ebpByType = [];
        $ebpByLogicalType = [];
        $ebpByStatut = [];
        $ebpByAvancement = [];
        $ebpByReference = [];
        $totalConnected = 0;
        $boxRefs = $this->loadBoxReferences();

        foreach ($ebpItems as $item) {
            $props = $item['properties'] ?? [];
            $bpType = $props['bp_typephy'] ?? 'Unknown';
            $bpLogType = $props['bp_typelog'] ?? 'Unknown';
            $bpStatut = $props['bp_statut'] ?? 'Unknown';
            $bpAvct = $props['bp_avct'] ?? null;
            $caNb = (int) ($props['bp_ca_nb'] ?? 0);
            $rfCode = $props['bp_rf_code'] ?? null;

            if (! isset($ebpByType[$bpType])) {
                $ebpByType[$bpType] = ['count' => 0, 'cassettes' => 0];
            }
            $ebpByType[$bpType]['count']++;
            $ebpByType[$bpType]['cassettes'] += $caNb;

            if (! isset($ebpByLogicalType[$bpLogType])) {
                $ebpByLogicalType[$bpLogType] = ['count' => 0, 'cassettes' => 0];
            }
            $ebpByLogicalType[$bpLogType]['count']++;
            $ebpByLogicalType[$bpLogType]['cassettes'] += $caNb;

            if (! isset($ebpByStatut[$bpStatut])) {
                $ebpByStatut[$bpStatut] = ['count' => 0];
            }
            $ebpByStatut[$bpStatut]['count']++;

            if ($bpAvct !== null && $bpAvct !== '') {
                if (! isset($ebpByAvancement[$bpAvct])) {
                    $ebpByAvancement[$bpAvct] = ['count' => 0];
                }
                $ebpByAvancement[$bpAvct]['count']++;
            }

            $totalConnected += $caNb;

            if ($rfCode) {
                $refData = $boxRefs[$rfCode] ?? null;
                $refKey = $rfCode;

                if (! isset($ebpByReference[$refKey])) {
                    $ebpByReference[$refKey] = [
                        'rf_code' => $rfCode,
                        'designation' => $refData['designation'] ?? $rfCode,
                        'description' => $refData['description'] ?? '',
                        'manufacturer' => $refData['manufacturer'] ?? 'Unknown',
                        'logical_type' => $bpLogType,
                        'statut' => $bpStatut,
                        'avancement' => $bpAvct,
                        'count' => 0,
                        'cassettes' => 0,
                    ];
                }
                $ebpByReference[$refKey]['count']++;
                $ebpByReference[$refKey]['cassettes'] += $caNb;
                $ebpByReference[$refKey]['logical_type'] = $bpLogType;
                if ($bpStatut !== 'Unknown') {
                    $ebpByReference[$refKey]['statut'] = $bpStatut;
                }
                if ($bpAvct !== null && $bpAvct !== '') {
                    $ebpByReference[$refKey]['avancement'] = $bpAvct;
                }
            }
        }

        $sitesByType = [];
        foreach ($sites as $site) {
            $props = $site['properties'] ?? [];
            $stType = $props['st_typelog'] ?? $props['st_typephy'] ?? 'Unknown';

            if (! isset($sitesByType[$stType])) {
                $sitesByType[$stType] = ['count' => 0];
            }
            $sitesByType[$stType]['count']++;
        }

        $sroZones = $geojson['t_zsro'] ?? [];
        $sroSite = null;
        $sroZone = null;
        $orgNames = $this->loadOrganismes();

        foreach ($sites as $site) {
            $props = $site['properties'] ?? [];
            if (($props['st_typelog'] ?? '') === 'SRO') {
                $sroSite = $props;
                break;
            }
        }

        if ($sroSite && ! empty($sroSite['st_nd_code'])) {
            foreach ($sroZones as $zone) {
                $props = $zone['properties'] ?? [];
                if (($props['zs_nd_code'] ?? '') === $sroSite['st_nd_code']) {
                    $sroZone = $props;
                    break;
                }
            }
        }

        $techPoints = $geojson['t_ptech'] ?? [];
        $techPointsByStatut = [];

        foreach ($techPoints as $tp) {
            $props = $tp['properties'] ?? [];
            $ptStatut = $props['pt_statut'] ?? 'Unknown';
            $ptProp = $props['pt_prop'] ?? 'Unknown';
            $ptType = $props['pt_typephy'] ?? 'Unknown';

            if (! isset($techPointsByStatut[$ptStatut])) {
                $techPointsByStatut[$ptStatut] = ['count' => 0, 'by_owner' => []];
            }

            $techPointsByStatut[$ptStatut]['count']++;

            if (! isset($techPointsByStatut[$ptStatut]['by_owner'][$ptProp])) {
                $techPointsByStatut[$ptStatut]['by_owner'][$ptProp] = [];
            }

            $techPointsByStatut[$ptStatut]['by_owner'][$ptProp][$ptType] = ($techPointsByStatut[$ptStatut]['by_owner'][$ptProp][$ptType] ?? 0) + 1;
        }

        $ptechOwnerByNode = [];
        foreach ($geojson['t_ptech'] ?? [] as $pt) {
            $p = $pt['properties'] ?? [];
            $nd = $p['pt_nd_code'] ?? null;
            if ($nd && ! empty($p['pt_prop'])) {
                $ptechOwnerByNode[$nd] = $p['pt_prop'];
            }
        }

        $orgs = $this->loadOrganismes();
        $orangeCode = array_search('ORANGE', $orgs) ?: 'OR000000000001';
        $enedisCode = array_search('ENEDIS', $orgs) ?: 'OR000000000002';

        $conduitsByStatut = [];

        foreach ($pathways as $path) {
            $props = $path['properties'] ?? [];
            $s = $props['cm_statut'] ?? 'Unknown';
            $l = (float) ($props['cm_long'] ?? 0);
            $typImp = $props['cm_typ_imp'] ?? '';
            $isAerial = in_array($typImp, ['0', '1'], true);
            $isUnderground = in_array($typImp, ['4', '5', '6', '7', '8'], true);
            $nd1 = $props['cm_ndcode1'] ?? null;
            $nd2 = $props['cm_ndcode2'] ?? null;
            $own1 = $nd1 ? ($ptechOwnerByNode[$nd1] ?? null) : null;
            $own2 = $nd2 ? ($ptechOwnerByNode[$nd2] ?? null) : null;

            if ($own1 === $enedisCode && $own2 === $enedisCode) {
                $owner = $enedisCode;
            } elseif ($own1 === $orangeCode || $own2 === $orangeCode) {
                $owner = $orangeCode;
            } else {
                $owner = $own1 ?? $own2 ?? 'Unknown';
            }

            if ($isAerial) {
                $key = 'aerial_length';
            } elseif ($isUnderground) {
                $key = 'underground_length';
            } else {
                $key = 'facade_other_length';
            }

            if (! isset($conduitsByStatut[$s])) {
                $conduitsByStatut[$s] = ['by_owner' => []];
            }

            if (! isset($conduitsByStatut[$s]['by_owner'][$owner])) {
                $conduitsByStatut[$s]['by_owner'][$owner] = ['aerial_length' => 0.0, 'underground_length' => 0.0, 'facade_other_length' => 0.0];
            }

            $conduitsByStatut[$s]['by_owner'][$owner][$key] += $l;
        }

        $pboZones = $geojson['t_zpbo'] ?? [];
        $totalLogements = 0;
        $totalCapacity = 0;

        foreach ($sroZones as $zone) {
            $props = $zone['properties'] ?? [];
            $totalLogements += (int) ($props['zs_nblogmt'] ?? 0);
            $totalCapacity += (int) ($props['zs_capamax'] ?? 0);
        }

        $addresses = $geojson['t_adresse'] ?? [];
        $addrTotal = count($addresses);
        $addrNbprhab = 0;
        $addrNbprpro = 0;
        $addrNblhab = 0;
        $addrNblpro = 0;
        $addrByTypeim = [];
        $addrImneuf = 0;

        foreach ($addresses as $addr) {
            $p = $addr['properties'] ?? [];
            $addrNbprhab += (int) ($p['ad_nbprhab'] ?? 0);
            $addrNbprpro += (int) ($p['ad_nbprpro'] ?? 0);
            $addrNblhab += (int) ($p['ad_nblhab'] ?? 0);
            $addrNblpro += (int) ($p['ad_nblpro'] ?? 0);

            $typeim = $p['ad_itypeim'] ?? 'Unknown';
            $addrByTypeim[$typeim] = ($addrByTypeim[$typeim] ?? 0) + 1;

            if (! empty($p['ad_imneuf']) && filter_var($p['ad_imneuf'], FILTER_VALIDATE_BOOLEAN)) {
                $addrImneuf++;
            }
        }

        $nodes = $geojson['t_noeud'] ?? [];
        $nodesByType = [];

        foreach ($nodes as $node) {
            $props = $node['properties'] ?? [];
            $ndType = $props['nd_type'] ?? 'Unknown';

            if (! isset($nodesByType[$ndType])) {
                $nodesByType[$ndType] = ['count' => 0];
            }
            $nodesByType[$ndType]['count']++;
        }

        $cableLines = $geojson['t_cableline'] ?? [];
        $cableLineTotalLength = 0.0;

        foreach ($cableLines as $cl) {
            $props = $cl['properties'] ?? [];
            $cableLineTotalLength += (float) ($props['cl_long'] ?? 0);
        }

        $stats['detailed'] = [
            'cables' => [
                'total_count' => count($cables),
                'total_length_m' => round($cableTotalLength, 2),
                'geometric_length_m' => round($cableLineTotalLength, 2),
                'by_reference' => array_values($cablesByReference),
            ],
            'fibers' => [
                'total_capacity' => $totalFibers,
                'total_used' => $usedFibers,
                'total_available' => $totalFibers - $usedFibers,
                'spare_fibers' => max(0, $spareFibers),
                'occupation_rate' => $totalFibers > 0 ? round(($usedFibers / $totalFibers) * 100, 2) : 0,
            ],
            'pathways' => [
                'total_count' => count($pathways),
                'total_length_m' => round($pathwayTotalLength, 2),
                'by_implantation_type' => $pathwaysByImpType,
                'by_logical_type' => $pathwaysByLogType,
            ],
            'equipment' => [
                'sites' => [
                    'total' => count($sites),
                    'by_type' => $sitesByType,
                ],
                'optical_boxes' => [
                    'total' => count($ebpItems),
                    'total_cassettes' => $totalConnected,
                    'by_type' => $ebpByType,
                    'by_logical_type' => $ebpByLogicalType,
                    'by_statut' => $ebpByStatut,
                    'by_avancement' => $ebpByAvancement,
                    'by_reference' => array_values($ebpByReference),
                ],
            ],
            'supports' => [
                'organismes' => $this->loadOrganismes(),
                'technical_points' => [
                    'total' => count($techPoints),
                    'by_statut' => $techPointsByStatut,
                ],
                'conduits' => [
                    'by_statut' => $conduitsByStatut,
                ],
            ],
            'logements' => [
                'logements' => [
                    'total' => $totalLogements,
                    'max_capacity' => $totalCapacity,
                    'occupation_rate' => $totalCapacity > 0 ? round(($totalLogements / $totalCapacity) * 100, 2) : 0,
                ],
                'connected' => $totalConnected,
                'sro_zone_count' => count($sroZones),
                'pbo_zone_count' => count($pboZones),
            ],
            'addresses' => [
                'total' => $addrTotal,
                'prises_habitation' => $addrNbprhab,
                'prises_professionnelles' => $addrNbprpro,
                'locaux_habitation' => $addrNblhab,
                'locaux_professionnels' => $addrNblpro,
                'immeubles_neufs' => $addrImneuf,
                'by_type_immeuble' => $addrByTypeim,
            ],
            'sro' => [
                'site' => $sroSite,
                'zone' => $sroZone,
                'organismes' => $orgNames,
            ],
        ];

        return $stats;
    }

    private function pointInPolygon(array $point, array $polygon): bool
    {
        $x = $point[0];
        $y = $point[1];
        $inside = false;
        $n = count($polygon);

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            if ((($yi > $y) !== ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi)) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }

    private function euclideanDistance(array $a, array $b): float
    {
        return sqrt(pow($a[0] - $b[0], 2) + pow($a[1] - $b[1], 2));
    }

    private function projectPointOnLine(array $point, array $lineCoords): array
    {
        $bestDist = INF;
        $bestDistAlong = 0;
        $cumulativeDist = 0;
        $n = count($lineCoords);

        for ($i = 0; $i < $n - 1; $i++) {
            $segStart = $lineCoords[$i];
            $segEnd = $lineCoords[$i + 1];
            $segLen = $this->euclideanDistance($segStart, $segEnd);

            $dx = $segEnd[0] - $segStart[0];
            $dy = $segEnd[1] - $segStart[1];
            $lenSq = $dx * $dx + $dy * $dy;

            if ($lenSq === 0.0) {
                $dist = $this->euclideanDistance($point, $segStart);
                if ($dist < $bestDist) {
                    $bestDist = $dist;
                    $bestDistAlong = $cumulativeDist;
                }

                continue;
            }

            $t = (($point[0] - $segStart[0]) * $dx + ($point[1] - $segStart[1]) * $dy) / $lenSq;
            $t = max(0, min(1, $t));

            $projX = $segStart[0] + $t * $dx;
            $projY = $segStart[1] + $t * $dy;
            $dist = $this->euclideanDistance($point, [$projX, $projY]);

            if ($dist < $bestDist) {
                $bestDist = $dist;
                $bestDistAlong = $cumulativeDist + $t * $segLen;
            }

            $cumulativeDist += $segLen;
        }

        return [
            'distance' => $bestDistAlong,
            'line_length' => $cumulativeDist,
        ];
    }

    private function calculateFiberPerPBO(array $geojson): array
    {
        $zpboZones = $geojson['t_zpbo'] ?? [];
        $addresses = $geojson['t_adresse'] ?? [];
        $cables = $geojson['t_cable'] ?? [];
        $sites = $geojson['t_sitetech'] ?? [];
        $cheminement = $geojson['t_cheminement'] ?? [];

        // --- Build cable graph ---
        $graph = [];
        foreach ($cables as $c) {
            $p = $c['properties'] ?? [];
            $n1 = $p['cb_nd1'] ?? null;
            $n2 = $p['cb_nd2'] ?? null;
            $capafo = (int) ($p['cb_capafo'] ?? 0);
            $cbCode = $p['cb_code'] ?? '';
            $cableLength = (float) ($p['cb_lgreel'] ?? 0);
            if ($n1) {
                $graph[$n1][] = ['cable' => $cbCode, 'capacity' => $capafo, 'other' => $n2, 'length_m' => $cableLength];
            }
            if ($n2) {
                $graph[$n2][] = ['cable' => $cbCode, 'capacity' => $capafo, 'other' => $n1, 'length_m' => $cableLength];
            }
        }

        // --- Build distance-weighted graph from cheminement ---
        $distGraph = [];
        foreach ($cheminement as $ch) {
            $chProps = $ch['properties'] ?? [];
            $n1 = $chProps['ch_ext_nd_id'] ?? null;
            $n2 = $chProps['ch_ext_nd_id_2'] ?? null;
            $dist = (float) ($chProps['cm_long'] ?? 0);
            if ($n1 && $n2) {
                $distGraph[$n1][] = ['node' => $n2, 'distance' => $dist];
                $distGraph[$n2][] = ['node' => $n1, 'distance' => $dist];
            }
        }

        // --- Find SRO node(s) from t_sitetech. Prefer SRO; fall back to NRO. ---
        $sourceNodes = [];
        foreach ($sites as $site) {
            $props = $site['properties'] ?? [];
            $type = $props['st_typelog'] ?? $props['st_typephy'] ?? '';
            $ndCode = $props['st_nd_code'] ?? null;
            if (! $ndCode) {
                continue;
            }
            if ($type === 'SRO') {
                $sourceNodes['SRO'][] = $ndCode;
            } elseif ($type === 'NRO') {
                $sourceNodes['NRO'][] = $ndCode;
            }
        }

        $siteType = isset($sourceNodes['SRO']) ? 'SRO' : (isset($sourceNodes['NRO']) ? 'NRO' : null);
        $poolNodes = $sourceNodes[$siteType] ?? [];

        // --- Find feeder cables (cables connected to the pool nodes) ---
        $feeders = [];
        foreach ($poolNodes as $ndCode) {
            if (! isset($graph[$ndCode])) {
                continue;
            }
            foreach ($graph[$ndCode] as $edge) {
                $feeders[] = [
                    'site_type' => $siteType,
                    'pool_node' => $ndCode,
                    'cable_code' => $edge['cable'],
                    'capacity' => $edge['capacity'],
                    'far_node' => $edge['other'],
                    'length_m' => $edge['length_m'] ?? 0,
                ];
            }
        }

        // --- Calculate per-PBO fiber_utile ---
        $pboData = [];
        foreach ($zpboZones as $zone) {
            $props = $zone['properties'] ?? [];
            $geometry = $zone['geometry'] ?? null;
            $zpCode = $props['zp_code'] ?? '';
            $zpNode = $props['zp_nd_code'] ?? null;

            if (! $geometry || ! $zpCode) {
                continue;
            }

            $coords = $geometry['coordinates'] ?? null;
            $type = $geometry['type'] ?? '';

            if (! $coords) {
                continue;
            }

            $ring = match ($type) {
                'Polygon' => $coords[0] ?? null,
                'MultiPolygon' => $coords[0][0] ?? null,
                default => null,
            };

            if (! $ring || ! is_array($ring[0] ?? null)) {
                continue;
            }

            $fiberUtile = 0;
            $addrCount = 0;
            foreach ($addresses as $addr) {
                $addrProps = $addr['properties'] ?? [];
                $addrGeom = $addr['geometry'] ?? null;
                if (! $addrGeom || ! isset($addrGeom['coordinates'])) {
                    continue;
                }

                $addrCoords = $addrGeom['coordinates'];
                $addrGeomType = $addrGeom['type'] ?? 'Point';
                $point = $addrGeomType === 'MultiPoint' ? ($addrCoords[0] ?? $addrCoords) : $addrCoords;

                if ($this->pointInPolygon($point, $ring)) {
                    $addrCount++;
                    $nbprhab = (int) ($addrProps['ad_nbprhab'] ?? 0);
                    $nbprpro = (int) ($addrProps['ad_nbprpro'] ?? 0);
                    $fiberUtile += $nbprhab + ($nbprpro * 2);
                }
            }

            $pboData[$zpCode] = [
                'zp_code' => $zpCode,
                'zp_node' => $zpNode,
                'prises' => $addrCount,
                'fiber_utile' => $fiberUtile,
            ];
        }

        // --- BFS from each feeder cable to find downstream PBOs ---
        $anomalies = [];
        $feederResults = [];
        $assignedPboNodes = [];
        $totalUtile = 0;
        $totalDisponible = 0;

        foreach ($feeders as $feeder) {
            $visited = [$feeder['pool_node'] => true];
            $queue = [$feeder['far_node']];
            $visited[$feeder['far_node']] = true;
            $foundPbos = [];

            while (! empty($queue)) {
                $current = array_shift($queue);

                foreach ($pboData as $zpCode => $data) {
                    if ($data['zp_node'] === $current && ! isset($assignedPboNodes[$zpCode])) {
                        $assignedPboNodes[$zpCode] = $feeder['cable_code'];
                        $foundPbos[] = $data;
                    }
                }

                if (isset($graph[$current])) {
                    foreach ($graph[$current] as $edge) {
                        if (! isset($visited[$edge['other']])) {
                            $visited[$edge['other']] = true;
                            $queue[] = $edge['other'];
                        }
                    }
                }
            }

            if (! empty($foundPbos)) {
                $sumUtile = array_sum(array_column($foundPbos, 'fiber_utile'));
                $dispo = $feeder['capacity'] > 0 ? max(0, $feeder['capacity'] - $sumUtile) : 0;

                $totalUtile += $sumUtile;
                $totalDisponible += $dispo;

                $feederResults[] = [
                    'site_type' => $feeder['site_type'],
                    'cable_code' => $feeder['cable_code'],
                    'capacity' => $feeder['capacity'],
                    'total_utile' => $sumUtile,
                    'total_disponible' => $dispo,
                    'zones' => $foundPbos,
                ];

                if ($feeder['capacity'] > 0 && $sumUtile > $feeder['capacity']) {
                    $anomalies[] = [
                        'type' => 'fiber_saturation',
                        'severity' => 'critical',
                        'shp' => 't_cable',
                        'message' => "Feeder cable {$feeder['cable_code']}: fiber saturation ({$sumUtile} cumulative > {$feeder['capacity']} capacity)",
                        'solution' => 'Augmenter la capacité du câble feeder ou ajouter un nouveau feeder',
                    ];
                }
            }
        }

        // --- Épissurage detection for cables > 3000m ---
        $epissurages = [];
        foreach ($feeders as $feeder) {
            if ($feeder['length_m'] <= 3000) {
                continue;
            }

            $distances = $this->shortestDistances($distGraph, $feeder['pool_node']);

            $orderedPboDist = [];
            foreach ($feederResults as $fr) {
                if ($fr['cable_code'] !== $feeder['cable_code']) {
                    continue;
                }
                foreach ($fr['zones'] as $zone) {
                    $node = $zone['zp_node'] ?? '';
                    $d = $distances[$node] ?? null;
                    if ($d !== null) {
                        $orderedPboDist[] = [
                            'zp_code' => $zone['zp_code'],
                            'zp_node' => $node,
                            'fiber_utile' => $zone['fiber_utile'],
                            'distance_from_sro' => $d,
                        ];
                    }
                }
            }

            if (empty($orderedPboDist)) {
                continue;
            }

            usort($orderedPboDist, fn ($a, $b) => $a['distance_from_sro'] <=> $b['distance_from_sro']);

            $points = [];
            $cumDist = 0;
            $prevDist = 0;
            $downstreamUtile = array_sum(array_column($orderedPboDist, 'fiber_utile'));

            foreach ($orderedPboDist as $pbo) {
                $segDist = $pbo['distance_from_sro'] - $prevDist;
                $cumDist += $segDist;
                $prevDist = $pbo['distance_from_sro'];

                if ($cumDist > 3000) {
                    $points[] = [
                        'node_code' => $pbo['zp_node'],
                        'box_type' => 'PBO',
                        'zp_code' => $pbo['zp_code'],
                        'distance_m' => $pbo['distance_from_sro'],
                        'fibre_utile_epissuree' => $downstreamUtile,
                    ];
                    $cumDist = 0;
                }

                $downstreamUtile -= $pbo['fiber_utile'];
            }

            if (! empty($points)) {
                $epissurages[] = [
                    'cable_code' => $feeder['cable_code'],
                    'capacity' => $feeder['capacity'],
                    'length_m' => $feeder['length_m'],
                    'points' => $points,
                ];
            }
        }

        // --- Unassigned PBOs ---
        foreach ($pboData as $zpCode => $data) {
            if (! isset($assignedPboNodes[$zpCode])) {
                $anomalies[] = [
                    'type' => 'fiber_no_feeder',
                    'severity' => 'warning',
                    'shp' => 't_zpbo',
                    'message' => "PBO zone {$zpCode}: not reachable from any {$siteType} feeder cable",
                    'solution' => "Vérifier le raccordement de la zone PBO {$zpCode} au réseau {$siteType}",
                ];
            }
        }

        return [
            'stats' => [
                'pbo_count' => count($pboData),
                'total_fiber_utile' => $totalUtile,
                'total_fiber_disponible' => $totalDisponible,
                'feeder_cables' => $feederResults,
                'operations_chantier' => [
                    'epissurages' => $epissurages,
                ],
            ],
            'anomalies' => $anomalies,
        ];
    }

    public function calculateScores(array $anomalies, array $stats, array $geojson): array
    {
        $orphanCount = 0;
        foreach ($anomalies as $a) {
            if (str_contains($a['message'], 'orphan') || str_contains($a['message'], 'without')) {
                $orphanCount++;
            }
        }

        $connectivityScore = max(0, min(100, 100 - ($orphanCount * 10)));

        $coherenceScore = max(0, min(100, 100 - count($anomalies) * 0.1));

        $fpb = $stats['detailed']['fibers_per_pbo'] ?? null;
        $totalCap = $fpb ? (int) array_sum(array_column($fpb['feeder_cables'] ?? [], 'capacity')) : 0;

        if ($totalCap > 0) {
            $graphUtile = (int) ($fpb['total_fiber_utile'] ?? 0);
            $graphDispo = (int) ($fpb['total_fiber_disponible'] ?? 0);

            $capacityScore = max(0, min(100, (int) round(100 - ($graphUtile / $totalCap) * 50)));
            $extensibilityScore = max(0, min(100, (int) round(($graphDispo / $totalCap) * 100)));
        } else {
            $occupationRate = $stats['occupation_rate'] ?? 0;
            if ($occupationRate >= 95) {
                $capacityScore = 20;
            } elseif ($occupationRate >= 80) {
                $capacityScore = 50;
            } elseif ($occupationRate > 0) {
                $capacityScore = 100 - ($occupationRate * 0.5);
            } else {
                $capacityScore = 100;
            }
            $capacityScore = max(0, min(100, (int) round($capacityScore)));

            $spareRatio = $stats['total_fibers'] > 0
                ? ($stats['spare_fibers'] / $stats['total_fibers'])
                : 1;

            $extensibilityScore = (int) round($spareRatio * 100);
        }

        $overall = round(
            $connectivityScore * 0.40 +
            $coherenceScore * 0.30 +
            $capacityScore * 0.20 +
            $extensibilityScore * 0.10,
            2
        );

        return [
            'connectivity' => $connectivityScore,
            'coherence' => $coherenceScore,
            'capacity' => $capacityScore,
            'extensibility' => $extensibilityScore,
            'overall' => $overall,
            'interpretation' => $this->interpretScore($overall),
            'capacity_threshold' => $this->getCapacityThreshold($totalCap > 0 ? round(($fpb['total_fiber_utile'] / $totalCap) * 100, 2) : ($stats['occupation_rate'] ?? 0)),
        ];
    }

    public function interpretScore(float $score): string
    {
        return match (true) {
            $score >= 90 => 'Excellent',
            $score >= 75 => 'Good',
            $score >= 50 => 'Acceptable',
            default => 'Non-compliant',
        };
    }

    public function getCapacityThreshold(float $occupationRate): string
    {
        return match (true) {
            $occupationRate >= 95 => 'Critical',
            $occupationRate >= 80 => 'Warning',
            default => 'Normal',
        };
    }

    private function shortestDistances(array $graph, string $start): array
    {
        $dist = [];
        $visited = [];
        $pq = new \SplPriorityQueue;
        $dist[$start] = 0;
        $pq->insert($start, 0);

        while (! $pq->isEmpty()) {
            $current = $pq->extract();
            if (isset($visited[$current])) {
                continue;
            }
            $visited[$current] = true;

            foreach ($graph[$current] ?? [] as $edge) {
                $neighbor = $edge['node'];
                $newDist = $dist[$current] + $edge['distance'];
                if (! isset($dist[$neighbor]) || $newDist < $dist[$neighbor]) {
                    $dist[$neighbor] = $newDist;
                    $pq->insert($neighbor, -$newDist);
                }
            }
        }

        return $dist;
    }
}
