<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Utilisateurs
 *
 * Gestion des comptes utilisateurs (agents et admins).
 * Toutes les routes de ce groupe sont réservées aux administrateurs,
 * sauf la modification et la consultation de son propre profil.
 */
class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    // ─── GET /api/users  (admin uniquement) ───────────────────────────────────
    /**
     * Liste des agents
     *
     * Retourne la liste paginée des agents immobiliers avec le nombre de biens associés.
     * Rôle requis : admin.
     *
     * @authenticated
     * @queryParam per_page integer Résultats par page. Défaut: 15. Example: 10
     *
     * @response 200 {
     *   "data": [{ "id": 2, "name": "Karim Benamara", "email": "karim@immobilier.dz", "role": "agent", "properties_count": 3 }],
     *   "meta": { "current_page": 1, "total": 3 }
     * }
     * @response 403 { "message": "Accès refusé. Rôle insuffisant." }
     */    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $perPage   = (int) $request->query('per_page', 15);
        $paginator = $this->userService->paginateAgents($perPage);

        return response()->json([
            'data' => UserResource::collection($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    // ─── GET /api/users/{user} ─────────────────────────────────────────────────
    /**
     * Détail d’un utilisateur
     *
     * Retourne le profil d’un utilisateur. Un utilisateur peut consulter son propre profil.
     *
     * @authenticated
     * @urlParam id integer required ID de l'utilisateur. Example: 2
     *
     * @response 200 { "id": 2, "name": "Karim Benamara", "email": "karim@immobilier.dz", "role": "agent" }
     * @response 403 { "message": "Action non autorisée." }
     * @response 404 { "message": "Ressource introuvable." }
     */    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findOrFail($id);
        $this->authorize('view', $user);

        return response()->json(new UserResource($user));
    }

    // ─── PUT /api/users/{user} ─────────────────────────────────────────────────
    /**
     * Modifier un utilisateur
     *
     * Met à jour le profil d’un utilisateur. Seul un admin peut changer le rôle.
     * Un utilisateur peut modifier son propre profil (sauf le rôle).
     *
     * @authenticated
     * @urlParam id integer required ID de l'utilisateur. Example: 2
     *
     * @bodyParam name string Nom complet. Example: Karim B.
     * @bodyParam phone string Numéro de téléphone. Example: +213 666 000111
     * @bodyParam role string Rôle (admin uniquement) : admin, agent, guest. Example: agent
     * @bodyParam is_active boolean Activer/désactiver le compte (admin uniquement). Example: true
     *
     * @response 200 { "id": 2, "name": "Karim B.", "role": "agent" }
     * @response 403 { "message": "Vous ne pouvez pas modifier ce profil." }
     * @response 404 { "message": "Ressource introuvable." }
     */    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $target = $this->userService->findOrFail($id);
        $this->authorize('update', $target);

        $updated = $this->userService->update($target, $request->validated(), $request->user());

        return response()->json(new UserResource($updated));
    }

    // ─── DELETE /api/users/{user}  (admin uniquement) ─────────────────────────
    /**
     * Supprimer un utilisateur
     *
     * Supprime définitivement un compte utilisateur.
     * Un admin ne peut pas se supprimer lui-même.
     * Rôle requis : admin.
     *
     * @authenticated
     * @urlParam id integer required ID de l'utilisateur. Example: 4
     *
     * @response 200 { "message": "Utilisateur supprimé avec succès." }
     * @response 403 { "message": "Seul un administrateur peut supprimer un utilisateur." }
     * @response 404 { "message": "Ressource introuvable." }
     */    public function destroy(Request $request, int $id): JsonResponse
    {
        $target = $this->userService->findOrFail($id);
        $this->authorize('delete', $target);

        $this->userService->delete($target, $request->user());

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }
}
