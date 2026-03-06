<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function paginateAgents(int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->paginateAgents($perPage);
    }

    public function findOrFail(int $id): User
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Utilisateur #{$id} introuvable.");
        }

        return $user;
    }

    /**
     * Met à jour le profil d'un utilisateur.
     * Un admin peut tout modifier ; un utilisateur ne peut modifier que son propre profil.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(User $target, array $data, User $authUser): User
    {
        if (!$authUser->isAdmin() && $authUser->id !== $target->id) {
            throw new AuthorizationException("Vous ne pouvez pas modifier ce profil.");
        }

        // Seul un admin peut changer le rôle
        if (isset($data['role']) && !$authUser->isAdmin()) {
            throw new AuthorizationException("Vous n'êtes pas autorisé à changer les rôles.");
        }

        // Exclure le mot de passe de cette méthode (flux séparé)
        unset($data['password']);

        return $this->userRepository->update($target, $data);
    }

    /**
     * Désactive ou supprime un agent (admin uniquement).
     *
     * @throws AuthorizationException
     */
    public function delete(User $target, User $authUser): void
    {
        if (!$authUser->isAdmin()) {
            throw new AuthorizationException("Seul un administrateur peut supprimer un utilisateur.");
        }

        if ($target->id === $authUser->id) {
            throw new \LogicException("Un administrateur ne peut pas se supprimer lui-même.");
        }

        $this->userRepository->delete($target);
    }
}
