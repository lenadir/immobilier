<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user): ?bool
    {
        // L'admin a accès à tout
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id;
    }

    public function delete(User $user, User $target): bool
    {
        // Un admin ne peut pas se supprimer lui-même
        return $user->isAdmin() && $user->id !== $target->id;
    }
}
