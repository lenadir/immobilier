<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;

/**
 * Policy de contrôle d'accès aux biens immobiliers.
 * Utilisée dans les controllers via $this->authorize() ou Gate::authorize().
 */
class PropertyPolicy
{
    /**
     * Un admin peut tout faire.
     * Retourner `true` ici évite de passer dans chaque méthode.
     */
    public function before(User $user): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null; // Laisse passer aux méthodes spécifiques
    }

    /**
     * Tout le monde peut voir les biens publiés.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Voir un bien : autorisé si publié, ou si l'agent en est propriétaire.
     */
    public function view(User $user, Property $property): bool
    {
        if ($property->is_published) {
            return true;
        }

        return $user->isAgent() && $property->user_id === $user->id;
    }

    /**
     * Créer un bien : agent ou admin.
     */
    public function create(User $user): bool
    {
        return $user->isAgent();
    }

    /**
     * Modifier un bien : agent propriétaire uniquement.
     */
    public function update(User $user, Property $property): bool
    {
        return $user->isAgent() && $property->user_id === $user->id;
    }

    /**
     * Supprimer un bien : agent propriétaire uniquement.
     */
    public function delete(User $user, Property $property): bool
    {
        return $user->isAgent() && $property->user_id === $user->id;
    }

    /**
     * Gérer les images : agent propriétaire uniquement.
     */
    public function manageImages(User $user, Property $property): bool
    {
        return $user->isAgent() && $property->user_id === $user->id;
    }

    /**
     * Restaurer un bien soft-deleté : admin uniquement.
     * (before() gère l'admin, mais on reste explicite)
     */
    public function restore(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Suppression définitive : admin uniquement.
     */
    public function forceDelete(User $user): bool
    {
        return $user->isAdmin();
    }
}
