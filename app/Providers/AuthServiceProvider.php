<?php

namespace App\Providers;

use App\Models\Audit;
use App\Models\Project;
use App\Models\User;
use App\Policies\AuditPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Project::class => ProjectPolicy::class,
        Audit::class => AuditPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
