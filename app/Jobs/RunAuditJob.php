<?php

namespace App\Jobs;

use App\Enums\AuditStatus;
use App\Models\Audit;
use App\Services\AuditService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RunAuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60];

    public int $timeout = 900;

    public function __construct(
        public int $auditId,
    ) {}

    public function middleware(): array
    {
        return [(new WithoutOverlapping((string) $this->auditId))->expireAfter(900)];
    }

    public function handle(AuditService $auditService): void
    {
        $audit = Audit::findOrFail($this->auditId);

        try {
            $audit->update([
                'status' => AuditStatus::Running,
                'started_at' => now(),
            ]);

            $dataset = $audit->dataset;

            if (! $dataset) {
                throw new \RuntimeException('Dataset not found for audit.');
            }

            $auditService->runAudit($dataset, $audit);

            AnalyzeAuditJob::dispatch($audit->id);
        } catch (Throwable $e) {
            if ($this->attempts() < $this->tries) {
                throw $e;
            }

            $audit->update([
                'status' => AuditStatus::Failed,
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
