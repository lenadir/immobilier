<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\FilterPropertiesDTO;
use App\DTOs\UpdatePropertyDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\CreatePropertyRequest;
use App\Http\Requests\Property\FilterPropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Http\Resources\PropertyCollection;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Biens immobiliers
 *
 * Gestion des annonces de biens (appartements, villas, terrains, etc.).
 */
class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertyService $propertyService,
    ) {}

    // ─── GET /api/properties ─────────────────────────────────────────────────

    /**
     * Liste des biens
     *
     * Retourne la liste paginée des biens immobiliers avec filtres optionnels.
     * Les agents ne voient que leurs propres biens. Les admins voient tout.
     *
     * @authenticated
     * @queryParam city string Filtrer par ville. Example: Alger
     * @queryParam type string Type de bien (appartement, villa, terrain, bureau, commerce, maison, studio). Example: villa
     * @queryParam status string Statut du bien (disponible, vendu, location). Example: disponible
     * @queryParam min_price number Prix minimum en DA. Example: 5000000
     * @queryParam max_price number Prix maximum en DA. Example: 50000000
     * @queryParam search string Recherche full-text sur le titre et la description. Example: piscine
     * @queryParam per_page integer Nombre de résultats par page (défaut: 15, max: 100). Example: 10
     * @queryParam sort_by string Champ de tri : price, created_at, surface, rooms. Example: price
     * @queryParam sort_dir string Direction du tri : asc ou desc. Example: asc
     *
     * @response 200 {
     *   "data": [{ "id": 1, "title": "Villa 4 pièces à Alger", "type": "villa", "price": 38000000 }],
     *   "meta": { "current_page": 1, "last_page": 3, "per_page": 15, "total": 42 }
     * }
     */
    public function index(FilterPropertyRequest $request): PropertyCollection
    {
        $filters = FilterPropertiesDTO::fromArray($request->validated());
        $paginator = $this->propertyService->list($filters, $request->user());

        return new PropertyCollection($paginator);
    }

    // ─── GET /api/properties/{id} ─────────────────────────────────────────────
    /**
     * Détail d’un bien
     *
     * Retourne les informations complètes d’un bien et ses images.
     * Les guests ne peuvent voir que les biens publiés.
     *
     * @authenticated
     * @urlParam id integer required ID du bien. Example: 1
     *
     * @response 200 {
     *   "id": 1, "title": "Villa 4 pièces à Alger", "type": "villa",
     *   "agent": { "id": 2, "name": "Karim Benamara" },
     *   "images": [{ "id": 1, "url": "http://localhost/storage/properties/1/photo.jpg", "is_cover": true }]
     * }
     * @response 403 { "message": "Ce bien n'est pas disponible." }
     * @response 404 { "message": "Ressource introuvable." }
     */    public function show(Request $request, int $id): JsonResponse
    {
        $property = $this->propertyService->show($id, $request->user());

        return response()->json(new PropertyResource($property));
    }

    // ─── POST /api/properties ─────────────────────────────────────────────────
    /**
     * Créer un bien
     *
     * Crée une nouvelle annonce immobilière. Le titre est généré automatiquement.
     * Rôles autorisés : admin, agent.
     *
     * @authenticated
     * @bodyParam type string required Type de bien : appartement, villa, terrain, bureau, commerce, maison, studio. Example: villa
     * @bodyParam price number required Prix en DA. Example: 38000000
     * @bodyParam city string required Ville du bien. Example: Alger
     * @bodyParam status string Statut (disponible, vendu, location). Défaut: disponible. Example: disponible
     * @bodyParam rooms integer Nombre de pièces. Example: 4
     * @bodyParam surface number Surface en m². Example: 250.5
     * @bodyParam address string Adresse complète. Example: Bab Ezzouar, Alger
     * @bodyParam description string Description du bien (max 5000 caractères). Example: Magnifique villa avec piscine.
     * @bodyParam is_published boolean Publier immédiatement. Défaut: false. Example: true
     *
     * @response 201 { "id": 5, "title": "Villa 4 pièces à Alger", "type": "villa", "price": 38000000 }
     * @response 403 { "message": "Action non autorisée." }
     * @response 422 { "message": "Les données fournies sont invalides.", "errors": { "type": ["Le type de bien est obligatoire."] } }
     */    public function store(CreatePropertyRequest $request): JsonResponse
    {
        $this->authorize('create', Property::class);

        $dto = CreatePropertyDTO::fromArray($request->user()->id, $request->validated());
        $property = $this->propertyService->create($dto);

        return response()->json(new PropertyResource($property), 201);
    }

    // ─── PUT/PATCH /api/properties/{property} ─────────────────────────────────
    /**
     * Modifier un bien
     *
     * Met à jour les champs d’un bien (PATCH = mise à jour partielle acceptée).
     * Le titre est régénéré si type, rooms, city ou status changent.
     * Rôles autorisés : admin, agent (propriétaire uniquement).
     *
     * @authenticated
     * @urlParam property integer required ID du bien. Example: 5
     * @bodyParam type string Type de bien. Example: appartement
     * @bodyParam price number Prix en DA. Example: 25000000
     * @bodyParam city string Ville. Example: Oran
     * @bodyParam status string Statut. Example: vendu
     * @bodyParam rooms integer Nombre de pièces. Example: 3
     * @bodyParam surface number Surface en m². Example: 120
     * @bodyParam description string Description. Example: Bel appartement rénové.
     * @bodyParam is_published boolean Publier le bien. Example: true
     *
     * @response 200 { "id": 5, "title": "Appartement 3 pièces à Oran - Vendu" }
     * @response 403 { "message": "Vous n'\u00eates pas autorisé à modifier ce bien." }
     * @response 404 { "message": "Ressource introuvable." }
     */    public function update(UpdatePropertyRequest $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $dto = UpdatePropertyDTO::fromArray($request->validated());
        $updated = $this->propertyService->update($property, $dto, $request->user());

        return response()->json(new PropertyResource($updated));
    }

    // ─── DELETE /api/properties/{property} ────────────────────────────────────

    /**
     * Supprimer un bien (soft delete)
     *
     * Déplace le bien dans la corbeille (suppression réversible).
     * Le bien n’est plus visible mais peut être restauré par un admin.
     * Rôles autorisés : admin, agent (propriétaire uniquement).
     *
     * @authenticated
     * @urlParam property integer required ID du bien. Example: 5
     *
     * @response 200 { "message": "Bien supprimé avec succès." }
     * @response 403 { "message": "Vous n'\u00eates pas autorisé à modifier ce bien." }
     * @response 404 { "message": "Ressource introuvable." }
     */
    public function destroy(Request $request, Property $property): JsonResponse
    {
        $this->authorize('delete', $property);

        $this->propertyService->delete($property, $request->user());

        return response()->json(['message' => 'Bien supprimé avec succès.']);
    }

    // ─── GET /api/properties/trashed (corbeille) ───────────────────────────────────

    /**
     * Corbeille des biens supprimés
     *
     * Liste paginée des biens en corbeille (soft-deleted).
     * Rôle requis : admin uniquement.
     *
     * @authenticated
     * @queryParam per_page integer Résultats par page. Défaut: 15. Example: 10
     *
     * @response 200 {
     *   "data": [{ "id": 3, "title": "Studio 1 pièce à Annaba" }],
     *   "meta": { "current_page": 1, "total": 3 }
     * }
     * @response 403 { "message": "Seul un administrateur peut consulter la corbeille." }
     */
    public function trashed(Request $request): JsonResponse
    {
        $perPage   = (int) $request->query('per_page', 15);
        $paginator = $this->propertyService->trashed($request->user(), $perPage);

        return response()->json([
            'data' => PropertyResource::collection($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ]);
    }

    // ─── PATCH /api/properties/{property}/restore ──────────────────────────────────

    /**
     * Restaurer un bien
     *
     * Restaure un bien précédemment mis en corbeille.
     * Rôle requis : admin uniquement.
     *
     * @authenticated
     * @urlParam property integer required ID du bien en corbeille. Example: 3
     *
     * @response 200 { "message": "Bien restauré avec succès.", "data": { "id": 3, "title": "Studio 1 pièce à Annaba" } }
     * @response 403 { "message": "Seul un administrateur peut restaurer un bien." }
     * @response 404 { "message": "Ressource introuvable." }
     */
    public function restore(Request $request, int $property): JsonResponse
    {
        $model = $this->propertyService->findTrashedOrFail($property);
        $this->authorize('restore', Property::class);

        $restored = $this->propertyService->restore($model, $request->user());

        return response()->json([
            'message' => 'Bien restauré avec succès.',
            'data'    => new PropertyResource($restored),
        ]);
    }

    // ─── DELETE /api/properties/{property}/force (suppression définitive) ─────────────

    /**
     * Suppression définitive d’un bien
     *
     * Supprime irréversiblement un bien et toutes ses images depuis la corbeille.
     * Rôle requis : admin uniquement.
     *
     * @authenticated
     * @urlParam property integer required ID du bien en corbeille. Example: 3
     *
     * @response 200 { "message": "Bien définitivement supprimé." }
     * @response 403 { "message": "Seul un administrateur peut supprimer définitivement un bien." }
     * @response 404 { "message": "Ressource introuvable." }
     */
    public function forceDelete(Request $request, int $property): JsonResponse
    {
        $model = $this->propertyService->findTrashedOrFail($property);
        $this->authorize('forceDelete', Property::class);

        $this->propertyService->forceDelete($model, $request->user());

        return response()->json(['message' => 'Bien définitivement supprimé.']);
    }
}
