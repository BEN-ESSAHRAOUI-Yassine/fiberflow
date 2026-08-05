<?php

use App\Ai\Agents\AuditAnalystAgent;
use App\Jobs\AnalyzeAuditJob;
use App\Models\Audit;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

it('logs completion only on success', function () {
    $audit = Audit::factory()->for(Project::factory())->create([
        'quality_score' => 85.0,
    ]);

    $this->mock(AuditAnalystAgent::class)
        ->shouldReceive('analyze')
        ->andReturn([
            'summary' => 'ok',
            'quality' => 'ok',
            'observations' => [],
            'risks' => [],
            'recommendations' => ['rec 1'],
        ]);

    Log::spy();

    (new AnalyzeAuditJob($audit->id))->handle();

    Log::shouldHaveReceived('info')->once()->withArgs(fn ($message) => str_contains($message, 'completed'));
    Log::shouldNotHaveReceived('warning');

    expect($audit->fresh()->recommendations)->toBe(['rec 1']);
});

it('logs warning and no completion on failure', function () {
    $audit = Audit::factory()->for(Project::factory())->create();

    $this->mock(AuditAnalystAgent::class)
        ->shouldReceive('analyze')
        ->andThrow(new RuntimeException('API down'));

    Log::spy();

    $job = new AnalyzeAuditJob($audit->id);
    $job->tries = 1;
    $job->handle();

    Log::shouldHaveReceived('warning')->once()->withArgs(fn ($message) => str_contains($message, 'API down'));
    Log::shouldNotHaveReceived('info');

    expect($audit->fresh()->error_message)->toContain('API down');
});
