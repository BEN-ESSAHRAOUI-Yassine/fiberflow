<?php

namespace App\Ai\Tools;

use App\Services\AuditService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetMcdRules implements Tool
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function description(): Stringable|string
    {
        return 'Returns MCD (Modèle de Conception de Déploiement) rules for a given table and phase. Shows which fields are required at each project phase (PRO, EXE, REC). Use this to check if data complies with MCD requirements.';
    }

    public function handle(Request $request): Stringable|string
    {
        $table = $request['table'];
        $phase = $request['phase'] ?? 'PRO';

        $mcdRules = $this->auditService->loadMcdRules();
        $tableRules = $mcdRules[$table] ?? null;

        if (! $tableRules) {
            return json_encode([
                'error' => "No MCD rules found for table '{$table}'",
                'available_tables' => array_keys($mcdRules),
            ], JSON_UNESCAPED_UNICODE);
        }

        $requiredFields = $this->auditService->getRequiredFields($tableRules, $phase);

        $allFields = [];
        foreach ($tableRules as $field => $reqs) {
            $allFields[$field] = [
                'PRO' => $reqs['PRO'] ?? '-',
                'EXE_DISTRI' => $reqs['EXE_DISTRI'] ?? '-',
                'EXE_TRANSP' => $reqs['EXE_TRANSP'] ?? '-',
                'REC_TRANSP' => $reqs['REC_TRANSP'] ?? '-',
                'REC_DISTRI' => $reqs['REC_DISTRI'] ?? '-',
            ];
        }

        return json_encode([
            'table' => $table,
            'phase' => $phase,
            'required_fields' => $requiredFields,
            'all_fields' => $allFields,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'table' => $schema->string()->description('The table name to check rules for (e.g. t_cable, t_ebp, t_noeud)'),
            'phase' => $schema->string()->description('The project phase: PRO, EXE, or REC'),
        ];
    }
}
