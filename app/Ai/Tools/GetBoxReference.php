<?php

namespace App\Ai\Tools;

use App\Services\AuditService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetBoxReference implements Tool
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function description(): Stringable|string
    {
        return 'Looks up an optical box (EBP/boîte) reference by rf_code. Returns manufacturer, designation, and description. Use this to verify box specifications against reference data.';
    }

    public function handle(Request $request): Stringable|string
    {
        $rfCode = $request['rf_code'];
        $references = $this->auditService->loadBoxReferences();
        $ref = $references[$rfCode] ?? null;

        if (! $ref) {
            $available = array_keys($references);

            return json_encode([
                'error' => "Box reference '{$rfCode}' not found",
                'available_codes' => array_slice($available, 0, 50),
                'total_references' => count($available),
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode($ref, JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'rf_code' => $schema->string()->description('The box reference code to look up (e.g. RF-BP-001)'),
        ];
    }
}
