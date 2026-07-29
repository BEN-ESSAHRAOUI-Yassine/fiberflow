<?php

use App\Enums\UserRole;
use App\Models\Audit;
use App\Models\Project;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->engineer = User::factory()->create(['role' => UserRole::Ingenieur]);
});

describe('UserPolicy', function () {

    it('allows admin to view any users', function () {
        expect($this->admin->can('viewAny', User::class))->toBeTrue();
    });

    it('denies engineer to view any users', function () {
        expect($this->engineer->can('viewAny', User::class))->toBeFalse();
    });

    it('allows admin to create users', function () {
        expect($this->admin->can('create', User::class))->toBeTrue();
    });

    it('denies engineer to create users', function () {
        expect($this->engineer->can('create', User::class))->toBeFalse();
    });

    it('allows admin to update any user', function () {
        $target = User::factory()->create();

        expect($this->admin->can('update', $target))->toBeTrue();
    });

    it('denies engineer to update any user', function () {
        $target = User::factory()->create();

        expect($this->engineer->can('update', $target))->toBeFalse();
    });

    it('allows admin to delete users', function () {
        $target = User::factory()->create();

        expect($this->admin->can('delete', $target))->toBeTrue();
    });

    it('denies engineer to delete users', function () {
        $target = User::factory()->create();

        expect($this->engineer->can('delete', $target))->toBeFalse();
    });
});

describe('ProjectPolicy', function () {

    it('allows admin to view any project', function () {
        $project = Project::factory()->create();

        expect($this->admin->can('view', $project))->toBeTrue();
    });

    it('allows engineer to view own project', function () {
        $project = Project::factory()->create(['created_by' => $this->engineer]);

        expect($this->engineer->can('view', $project))->toBeTrue();
    });

    it('allows engineer to view another engineers project', function () {
        $other = User::factory()->create(['role' => UserRole::Ingenieur]);
        $project = Project::factory()->create(['created_by' => $other]);

        expect($this->engineer->can('view', $project))->toBeTrue();
    });

    it('allows admin to create projects', function () {
        expect($this->admin->can('create', Project::class))->toBeTrue();
    });

    it('denies engineer to create projects', function () {
        expect($this->engineer->can('create', Project::class))->toBeFalse();
    });

    it('allows admin to update any project', function () {
        $project = Project::factory()->create();

        expect($this->admin->can('update', $project))->toBeTrue();
    });

    it('denies engineer to update projects', function () {
        $project = Project::factory()->create();

        expect($this->engineer->can('update', $project))->toBeFalse();
    });

    it('allows admin to delete projects', function () {
        $project = Project::factory()->create();

        expect($this->admin->can('delete', $project))->toBeTrue();
    });

    it('denies engineer to delete projects', function () {
        $project = Project::factory()->create();

        expect($this->engineer->can('delete', $project))->toBeFalse();
    });
});

describe('AuditPolicy', function () {

    it('allows admin to view any audit', function () {
        $audit = Audit::factory()->create();

        expect($this->admin->can('view', $audit))->toBeTrue();
    });

    it('allows engineer to view any audit', function () {
        $audit = Audit::factory()->create();

        expect($this->engineer->can('view', $audit))->toBeTrue();
    });

    it('allows admin to launch audits', function () {
        expect($this->admin->can('create', Audit::class))->toBeTrue();
    });

    it('allows engineer to launch audits', function () {
        expect($this->engineer->can('create', Audit::class))->toBeTrue();
    });

    it('allows admin to update audits', function () {
        $audit = Audit::factory()->create();

        expect($this->admin->can('update', $audit))->toBeTrue();
    });

    it('denies engineer to update audits', function () {
        $audit = Audit::factory()->create();

        expect($this->engineer->can('update', $audit))->toBeFalse();
    });

    it('allows admin to delete audits', function () {
        $audit = Audit::factory()->create();

        expect($this->admin->can('delete', $audit))->toBeTrue();
    });

    it('denies engineer to delete audits', function () {
        $audit = Audit::factory()->create();

        expect($this->engineer->can('delete', $audit))->toBeFalse();
    });
});

describe('Restore & ForceDelete Policies', function () {

    it('allows admin to restore users', function () {
        $target = User::factory()->create();
        $target->delete();

        expect($this->admin->can('restore', $target))->toBeTrue();
    });

    it('denies engineer to restore users', function () {
        $target = User::factory()->create();
        $target->delete();

        expect($this->engineer->can('restore', $target))->toBeFalse();
    });

    it('allows admin to restore projects', function () {
        $project = Project::factory()->create();
        $project->delete();

        expect($this->admin->can('restore', $project))->toBeTrue();
    });

    it('denies engineer to restore projects', function () {
        $project = Project::factory()->create();
        $project->delete();

        expect($this->engineer->can('restore', $project))->toBeFalse();
    });

    it('allows admin to force delete users', function () {
        $target = User::factory()->create();
        $target->delete();

        expect($this->admin->can('forceDelete', $target))->toBeTrue();
    });

    it('denies engineer to force delete users', function () {
        $target = User::factory()->create();
        $target->delete();

        expect($this->engineer->can('forceDelete', $target))->toBeFalse();
    });

    it('allows admin to force delete projects', function () {
        $project = Project::factory()->create();
        $project->delete();

        expect($this->admin->can('forceDelete', $project))->toBeTrue();
    });

    it('denies engineer to force delete projects', function () {
        $project = Project::factory()->create();
        $project->delete();

        expect($this->engineer->can('forceDelete', $project))->toBeFalse();
    });
});
