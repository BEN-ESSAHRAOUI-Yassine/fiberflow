<?php

use App\Enums\AuditStatus;
use App\Enums\MessageRole;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Enums\StudyPhase;
use App\Enums\UserRole;

describe('AuditStatus enum', function () {

    it('has correct values', function () {
        expect(AuditStatus::Pending->value)->toBe('pending');
        expect(AuditStatus::Running->value)->toBe('running');
        expect(AuditStatus::Completed->value)->toBe('completed');
        expect(AuditStatus::Failed->value)->toBe('failed');
    });

    it('has 4 cases', function () {
        expect(AuditStatus::cases())->toHaveCount(4);
    });
});

describe('ProjectStatus enum', function () {

    it('has correct values', function () {
        expect(ProjectStatus::Draft->value)->toBe('draft');
        expect(ProjectStatus::InProgress->value)->toBe('in_progress');
        expect(ProjectStatus::Audited->value)->toBe('audited');
        expect(ProjectStatus::Validated->value)->toBe('validated');
        expect(ProjectStatus::Archived->value)->toBe('archived');
    });

    it('has 5 cases', function () {
        expect(ProjectStatus::cases())->toHaveCount(5);
    });

    it('has correct order', function () {
        expect(ProjectStatus::Draft->order())->toBe(1);
        expect(ProjectStatus::InProgress->order())->toBe(2);
        expect(ProjectStatus::Audited->order())->toBe(3);
        expect(ProjectStatus::Validated->order())->toBe(4);
        expect(ProjectStatus::Archived->order())->toBe(5);
    });

    it('returns all values via values()', function () {
        $values = ProjectStatus::values();
        expect($values)->toContain('draft');
        expect($values)->toContain('in_progress');
        expect($values)->toContain('archived');
    });
});

describe('StudyPhase enum', function () {

    it('has correct values', function () {
        expect(StudyPhase::APS->value)->toBe('APS');
        expect(StudyPhase::APD->value)->toBe('APD');
        expect(StudyPhase::PRO->value)->toBe('PRO');
        expect(StudyPhase::EXE->value)->toBe('EXE');
        expect(StudyPhase::REC->value)->toBe('REC');
        expect(StudyPhase::FIN->value)->toBe('FIN');
    });

    it('has 6 cases', function () {
        expect(StudyPhase::cases())->toHaveCount(6);
    });

    it('has correct order', function () {
        expect(StudyPhase::APS->order())->toBe(1);
        expect(StudyPhase::APD->order())->toBe(2);
        expect(StudyPhase::PRO->order())->toBe(3);
        expect(StudyPhase::EXE->order())->toBe(4);
        expect(StudyPhase::REC->order())->toBe(5);
        expect(StudyPhase::FIN->order())->toBe(6);
    });

    it('returns all values via values()', function () {
        $values = StudyPhase::values();
        expect($values)->toContain('APS');
        expect($values)->toContain('FIN');
    });
});

describe('ProjectType enum', function () {

    it('has correct values', function () {
        expect(ProjectType::Transport->value)->toBe('transport');
        expect(ProjectType::Distribution->value)->toBe('distribution');
    });

    it('has 2 cases', function () {
        expect(ProjectType::cases())->toHaveCount(2);
    });

    it('returns all values via values()', function () {
        $values = ProjectType::values();
        expect($values)->toContain('transport');
        expect($values)->toContain('distribution');
    });
});

describe('UserRole enum', function () {

    it('has correct values', function () {
        expect(UserRole::Admin->value)->toBe('admin');
        expect(UserRole::Ingenieur->value)->toBe('ingenieur');
    });

    it('has 2 cases', function () {
        expect(UserRole::cases())->toHaveCount(2);
    });

    it('returns all values via values()', function () {
        $values = UserRole::values();
        expect($values)->toContain('admin');
        expect($values)->toContain('ingenieur');
    });
});

describe('MessageRole enum', function () {

    it('has correct values', function () {
        expect(MessageRole::User->value)->toBe('user');
        expect(MessageRole::Assistant->value)->toBe('assistant');
    });

    it('has 2 cases', function () {
        expect(MessageRole::cases())->toHaveCount(2);
    });
});

describe('Enum order relationships', function () {

    it('ProjectStatus order is strictly increasing', function () {
        $statuses = ProjectStatus::cases();
        for ($i = 1; $i < count($statuses); $i++) {
            expect($statuses[$i]->order())->toBeGreaterThan($statuses[$i - 1]->order());
        }
    });

    it('StudyPhase order is strictly increasing', function () {
        $phases = StudyPhase::cases();
        for ($i = 1; $i < count($phases); $i++) {
            expect($phases[$i]->order())->toBeGreaterThan($phases[$i - 1]->order());
        }
    });
});
