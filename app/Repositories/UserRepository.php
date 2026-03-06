<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        $user->refresh();

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }

    public function paginateAgents(int $perPage = 15): LengthAwarePaginator
    {
        return User::where('role', User::ROLE_AGENT)
            ->withCount('properties')
            ->orderBy('name')
            ->paginate($perPage);
    }
}
