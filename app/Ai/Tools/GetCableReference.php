<?php

namespace App\Ai\Tools;

use App\Services\AuditService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetCableReference implements Tool
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function description(): Stringable|string
    {
        return 'Looks up a cable reference by rf_code. Returns manufacturer, designation, fiber count, modulo, and installation type. Use this to verify cable specifications against reference data.';
    }

    public function handle(Request $request): Stringable|string
    {
        $rfCode = $request['rf_code'];
        $references = $this->auditService->loadCableReferences();
        $ref = $references[$rfCode] ?? null;

        if (! $ref) {
            $available = array_keys($references);

            return json_encode([
                'error' => "Cable reference '{$rfCode}' not found",
                'available_codes' => array_slice($available, 0, 50),
                'total_references' => count($available),
            ], JSON_UNESCAPED_UNICODE);
        }

        return json_encode($ref, JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'rf_code' => $schema->string()->description('The cable reference code to look up (e.g. RF-CA-001)'),
        ];
    }
}
