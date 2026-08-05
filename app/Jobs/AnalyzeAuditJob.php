<?php

namespace App\Jobs;

use App\Ai\Agents\AuditAnalystAgent;
use App\Models\Audit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class AnalyzeAuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public function __construct(
        public int $auditId,
    ) {}

    public function handle(): void
    {
        $audit = Audit::with('project')->findOrFail($this->auditId);

        try {
            $agent = app(AuditAnalystAgent::class);
            $result = $agent->analyze($audit);

            $audit->update([
                'ai_summary' => json_encode($result, JSON_UNESCAPED_UNICODE),
                'recommendations' => $result['recommendations'],
            ]);

            Log::info("Audit {$audit->id} completed — score {$audit->quality_score}");
        } catch (Throwable $e) {
            Log::warning("AI analysis failed for audit {$audit->id}: {$e->getMessage()}");

            if ($this->attempts() < $this->tries) {
                throw $e;
            }

            $audit->update([
                'ai_summary' => json_encode([
                    'summary' => 'Analyse IA indisponible.',
                    'quality' => 'Non évalué.',
                    'observations' => [],
                    'risks' => [],
                    'recommendations' => [],
                ], JSON_UNESCAPED_UNICODE),
                'recommendations' => [],
                'error_message' => "Échec de l'analyse IA: {$e->getMessage()}",
            ]);
        }
    }
}
