<?php

namespace App\Jobs;

use App\Models\Audit;
use App\Services\AiAuditService;
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

    public int $tries = 2;

    public function __construct(
        public int $auditId,
    ) {}

    public function handle(AiAuditService $ai): void
    {
        $audit = Audit::with('project')->findOrFail($this->auditId);

        try {
            $result = $ai->analyze($audit);

            $audit->update([
                'ai_summary' => json_encode($result, JSON_UNESCAPED_UNICODE),
                'recommendations' => json_encode($result['recommendations'], JSON_UNESCAPED_UNICODE),
                'model_used' => config("ai.providers.{$ai->provider}.model", config('ai.providers.groq.model', 'meta-llama/llama-4-scout-17b-16e-instruct')),
                'tokens_used' => null,
            ]);
        } catch (Throwable $e) {
            Log::warning("AI analysis failed for audit {$audit->id}: {$e->getMessage()}");

            if ($this->attempts() < $this->tries) {
                $this->release(30);

                return;
            }

            $audit->update([
                'ai_summary' => json_encode([
                    'summary' => 'Analyse IA indisponible.',
                    'quality' => 'Non évalué.',
                    'observations' => [],
                    'risks' => [],
                    'recommendations' => [],
                ], JSON_UNESCAPED_UNICODE),
                'recommendations' => '[]',
            ]);
        }

        Log::info("Audit {$audit->id} completed — score {$audit->quality_score}");
    }
}
