<?php

namespace App\Policies;

use App\Models\Audit;
use App\Models\User;

class AuditPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Audit $audit): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Audit $audit): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Audit $audit): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Audit $audit): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Audit $audit): bool
    {
        return $user->isAdmin();
    }
}
