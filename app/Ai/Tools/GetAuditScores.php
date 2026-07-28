<?php

namespace App\Ai\Tools;

use App\Models\Audit;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetAuditScores implements Tool
{
    public function __construct(
        protected Audit $audit,
    ) {}

    public function description(): Stringable|string
    {
        return 'Returns quality scores for the current audit: overall, connectivity, coherence, capacity, extensibility. Use this to answer questions about audit quality ratings.';
    }

    public function handle(Request $request): Stringable|string
    {
        $scores = [
            'overall' => $this->audit->quality_score,
            'connectivity' => $this->audit->connectivity_score,
            'coherence' => $this->audit->coherence_score,
            'capacity' => $this->audit->capacity_score,
            'extensibility' => $this->audit->extensibility_score,
            'interpretation' => match (true) {
                $this->audit->quality_score >= 90 => 'Excellent',
                $this->audit->quality_score >= 75 => 'Bon',
                $this->audit->quality_score >= 50 => 'Acceptable',
                default => 'Non-conforme',
            },
        ];

        return json_encode($scores, JSON_UNESCAPED_UNICODE);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
