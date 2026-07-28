<?php

namespace App\Ai\Tools;

use App\Models\Audit;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetAnomalies implements Tool
{
    public function __construct(
        protected Audit $audit,
    ) {}

    public function description(): Stringable|string
    {
        return 'Returns anomalies detected in the audit. Optionally filter by type (transport, distribution, cable, ebp, fiber_saturation, fiber_no_feeder) and/or severity (critical, warning, info). Returns counts by type/severity and the matching anomaly list.';
    }

    public function handle(Request $request): Stringable|string
    {
        $anomalies = $this->audit->network_statistics['detailed']['anomalies'] ?? [];
        $type = $request['type'] ?? null;
        $severity = $request['severity'] ?? null;

        if ($type) {
            $anomalies = array_filter($anomalies, fn ($a) => ($a['type'] ?? '') === $type);
        }

        if ($severity) {
            $anomalies = array_filter($anomalies, fn ($a) => ($a['severity'] ?? '') === $severity);
        }

        $anomalies = array_values($anomalies);

        $counts = ['total' => 0, 'critical' => 0, 'warning' => 0, 'info' => 0, 'by_type' => []];
        $all = $this->audit->network_statistics['detailed']['anomalies'] ?? [];
        foreach ($all as $a) {
            $t = $a['type'] ?? 'unknown';
            $s = $a['severity'] ?? 'warning';
            $counts['total']++;
            $counts[$s] = ($counts[$s] ?? 0) + 1;
            $counts['by_type'][$t] = ($counts['by_type'][$t] ?? 0) + 1;
        }

        $maxResults = (int) ($request['limit'] ?? 20);
        $truncated = count($anomalies) > $maxResults;
        $anomalies = array_slice($anomalies, 0, $maxResults);

        return json_encode([
            'counts' => $counts,
            'truncated' => $truncated,
            'anomalies' => $anomalies,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()->description('Filter by anomaly type: transport, distribution, cable, ebp, fiber_saturation, fiber_no_feeder'),
            'severity' => $schema->string()->description('Filter by severity: critical, warning, info'),
            'limit' => $schema->integer()->description('Max anomalies to return (default 20)'),
        ];
    }
}
