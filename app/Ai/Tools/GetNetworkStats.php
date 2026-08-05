<?php

namespace App\Ai\Tools;

use App\Models\Audit;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetNetworkStats implements Tool
{
    public function __construct(
        protected Audit $audit,
    ) {}

    public function description(): Stringable|string
    {
        return 'Returns network statistics for the audit: cables (count, length, by reference), fibers (capacity, used, spare, occupation rate), pathways (count, length, by type), equipment (sites, optical boxes), addresses, logements, and SRO info.';
    }

    public function handle(Request $request): Stringable|string
    {
        $stats = $this->audit->network_statistics ?? [];
        $detailed = $stats['detailed'] ?? [];

        $cables = $detailed['cables'] ?? null;
        $fibers = $detailed['fibers'] ?? null;

        $summary = [];

        if ($cables) {
            $byRef = collect($cables['by_reference'] ?? [])->sortByDesc('count')->take(10)->values()->all();
            $summary['cables'] = [
                'total_count' => $cables['total_count'] ?? 0,
                'total_length_m' => $cables['total_length_m'] ?? 0,
                'top_references' => array_map(fn ($r) => [
                    'rf_code' => $r['rf_code'] ?? '',
                    'designation' => $r['designation'] ?? '',
                    'fiber_count' => $r['fiber_count'] ?? 0,
                    'count' => $r['count'] ?? 0,
                    'carto_length_m' => round($r['carto_length_m'] ?? 0, 1),
                ], $byRef),
            ];
        }

        if ($fibers) {
            $summary['fibers'] = [
                'total_capacity' => $fibers['total_capacity'] ?? 0,
                'total_used' => $fibers['total_used'] ?? 0,
                'spare_fibers' => $fibers['spare_fibers'] ?? 0,
                'occupation_rate' => $fibers['occupation_rate'] ?? 0,
            ];
        }

        $pathways = $detailed['pathways'] ?? null;
        if ($pathways) {
            $summary['pathways'] = [
                'total_count' => $pathways['total_count'] ?? 0,
                'total_length_m' => $pathways['total_length_m'] ?? 0,
                'by_implantation_type' => $pathways['by_implantation_type'] ?? $pathways['by_logical_type'] ?? [],
            ];
        }

        $equipment = $detailed['equipment'] ?? null;
        if ($equipment) {
            $summary['equipment'] = [
                'sites_total' => $equipment['sites']['total'] ?? 0,
                'optical_boxes' => [
                    'total' => $equipment['optical_boxes']['total'] ?? 0,
                    'total_cassettes' => $equipment['optical_boxes']['total_cassettes'] ?? 0,
                ],
            ];
        }

        $addresses = $detailed['addresses'] ?? null;
        if ($addresses) {
            $summary['addresses'] = ['total' => $addresses['total'] ?? 0];
        }

        $logements = $detailed['logements'] ?? null;
        if ($logements) {
            $summary['logements'] = ['total' => $logements['logements']['total'] ?? $logements['total'] ?? 0];
        }

        return json_encode(array_filter($summary), JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
