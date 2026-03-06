<?php

namespace App\Services;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\FilterPropertiesDTO;
use App\DTOs\UpdatePropertyDTO;
use App\Models\Property;
use App\Models\User;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PropertyService
{
    public function __construct(
        private readonly PropertyRepositoryInterface $propertyRepository,
    ) {}

    // ─── Liste ────────────────────────────────────────────────────────────────

    /**
     * Liste paginée des biens publiés (avec filtres).
     * Un admin peut voir tous les biens y compris non publiés.
     */
    public function list(FilterPropertiesDTO $filters, ?User $authUser): LengthAwarePaginator
    {
        if ($authUser && $authUser->isAgent()) {
            // L'agent voit uniquement ses propres biens
            return $this->propertyRepository->findByAgent($authUser->id, $filters);
        }

        return $this->propertyRepository->paginate($filters);
    }

    // ─── Détail ───────────────────────────────────────────────────────────────

    /**
     * Retourne un bien ou null si non trouvé.
     * Les guests ne voient que les biens publiés.
     */
    public function show(int $id, ?User $authUser): Property
    {
        $property = $this->propertyRepository->findById($id);

        if (!$property) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                "Bien immobilier #{$id} introuvable."
            );
        }

        // Unauthenticated users and guests can only see published properties
        if ((!$authUser || $authUser->isGuest()) && !$property->is_published) {
            throw new AuthorizationException("Ce bien n'est pas disponible.");
        }

        return $property;
    }

    // ─── Création ─────────────────────────────────────────────────────────────

    public function create(CreatePropertyDTO $dto): Property
    {
        return $this->propertyRepository->create($dto);
    }

    // ─── Mise à jour ──────────────────────────────────────────────────────────

    /**
     * @throws AuthorizationException si l'agent n'est pas propriétaire du bien
     */
    public function update(Property $property, UpdatePropertyDTO $dto, User $authUser): Property
    {
        $this->authorizeModification($property, $authUser);

        return $this->propertyRepository->update($property, $dto);
    }

    // ─── Suppression ──────────────────────────────────────────────────────────

    /**
     * @throws AuthorizationException si l'agent n'est pas propriétaire du bien
     */
    public function delete(Property $property, User $authUser): void
    {
        $this->authorizeModification($property, $authUser);

        $this->propertyRepository->delete($property);
    }

    // ─── Corbeille ────────────────────────────────────────────────────────────

    /**
     * Trouve un bien en corbeille ou lève ModelNotFoundException.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findTrashedOrFail(int $id): Property
    {
        $property = $this->propertyRepository->findTrashedById($id);

        if (!$property) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                "Bien immobilier #{$id} introuvable dans la corbeille."
            );
        }

        return $property;
    }

    /**
     * Liste paginée des biens supprimés (soft-deleted) — admin uniquement.
     *
     * @throws AuthorizationException
     */
    public function trashed(User $authUser, int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if (!$authUser->isAdmin()) {
            throw new AuthorizationException('Seul un administrateur peut consulter la corbeille.');
        }

        return $this->propertyRepository->trashed($perPage);
    }

    /**
     * Restaure un bien soft-deleté — admin uniquement.
     *
     * @throws AuthorizationException
     */
    public function restore(Property $property, User $authUser): Property
    {
        if (!$authUser->isAdmin()) {
            throw new AuthorizationException('Seul un administrateur peut restaurer un bien.');
        }

        return $this->propertyRepository->restore($property);
    }

    /**
     * Suppression définitive — admin uniquement.
     *
     * @throws AuthorizationException
     */
    public function forceDelete(Property $property, User $authUser): void
    {
        if (!$authUser->isAdmin()) {
            throw new AuthorizationException('Seul un administrateur peut supprimer définitivement un bien.');
        }

        $this->propertyRepository->forceDelete($property);
    }

    // ─── Helper d'autorisation (logique métier) ───────────────────────────────

    /**
     * Vérifie que l'utilisateur est autorisé à modifier le bien.
     * L'admin peut tout modifier ; l'agent uniquement son propre bien.
     *
     * @throws AuthorizationException
     */
    private function authorizeModification(Property $property, User $user): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isAgent() && $property->user_id === $user->id) {
            return;
        }

        throw new AuthorizationException("Vous n'êtes pas autorisé à modifier ce bien.");
    }
}
