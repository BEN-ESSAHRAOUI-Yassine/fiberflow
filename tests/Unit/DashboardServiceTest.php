<?php

use App\Models\Audit;
use App\Models\Project;
use App\Models\ProjectDataset;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

it('returns aggregate statistics visible to all users', function () {
    $user = User::factory()->engineer()->create();
    $project = Project::factory()->create();
    $dataset = ProjectDataset::factory()->create(['project_id' => $project->id]);
    Audit::factory()->completed()->for($project)->create([
        'projectdataset_id' => $dataset->id,
        'performed_by' => $user->id,
        'quality_score' => 80.0,
        'anomaly_count' => 10,
        'critical_anomaly_count' => 2,
    ]);
    Audit::factory()->completed()->for($project)->create([
        'projectdataset_id' => $dataset->id,
        'performed_by' => $user->id,
        'quality_score' => 60.0,
        'anomaly_count' => 4,
        'critical_anomaly_count' => 0,
    ]);

    $data = app(DashboardService::class)->summary($user);

    expect($data['projects_count'])->toBe(1);
    expect($data['audits_count'])->toBe(2);
    expect($data['average_quality_score'])->toBe(70.0);
    expect($data['total_anomalies'])->toBe(14);
    expect($data['total_critical_anomalies'])->toBe(2);
    expect($data)->toHaveKeys([
        'projects_by_type', 'projects_by_status', 'audits_by_status', 'recent_audits',
    ]);
});

it('scopes recent audits to the engineer own audits', function () {
    $admin = User::factory()->admin()->create();
    $engineer = User::factory()->engineer()->create();
    $project = Project::factory()->create();
    $own = Audit::factory()->completed()->for($project)->create(['performed_by' => $engineer->id]);
    Audit::factory()->completed()->for($project)->create(['performed_by' => $admin->id]);

    $data = app(DashboardService::class)->summary($engineer);

    expect($data['recent_audits'])->toHaveCount(1);
    expect($data['recent_audits'][0]['id'])->toBe($own->id);
});

it('shows all recent audits to admin', function () {
    $admin = User::factory()->admin()->create();
    $engineer = User::factory()->engineer()->create();
    $project = Project::factory()->create();
    Audit::factory()->completed()->for($project)->create(['performed_by' => $engineer->id]);
    Audit::factory()->completed()->for($project)->create(['performed_by' => $admin->id]);

    $data = app(DashboardService::class)->summary($admin);

    expect($data['recent_audits'])->toHaveCount(2);
});

it('includes all superset fields in recent audits', function () {
    $engineer = User::factory()->engineer()->create();
    $project = Project::factory()->create(['name' => 'Test Project']);
    Audit::factory()->completed()->for($project)->create(['performed_by' => $engineer->id]);

    $data = app(DashboardService::class)->summary($engineer);

    expect($data['recent_audits'][0])->toHaveKeys([
        'id', 'project_id', 'project_name', 'project_slug', 'status',
        'quality_score', 'anomaly_count', 'critical_anomaly_count',
        'performer_name', 'performer', 'created_at', 'completed_at',
    ]);
    expect($data['recent_audits'][0]['project_slug'])->toBe('test-project');
});

it('caches the summary per user', function () {
    $user = User::factory()->admin()->create();
    $project = Project::factory()->create();

    $first = app(DashboardService::class)->summary($user);
    expect($first['projects_count'])->toBe(1);

    Project::factory()->create();

    $cached = app(DashboardService::class)->summary($user);
    expect($cached['projects_count'])->toBe(1);

    Cache::flush();
    expect(app(DashboardService::class)->summary($user)['projects_count'])->toBe(2);
});
