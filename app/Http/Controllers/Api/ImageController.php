<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Image\UploadImageRequest;
use App\Http\Resources\ImageResource;
use App\Models\Image;
use App\Models\Property;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Images
 *
 * Upload et gestion des photos associées aux biens immobiliers.
 * Formats acceptés : JPEG, PNG, WebP. Taille maximale : 5 Mo par image.
 */
class ImageController extends Controller
{
    public function __construct(
        private readonly ImageService $imageService,
    ) {}

    // ─── POST /api/properties/{property}/images ───────────────────────────────
    /**
     * Uploader des images
     *
     * Envoie une ou plusieurs photos (max 10) pour un bien immobilier.
     * La première image uploadée devient automatiquement la couverture si aucune n'existe.
     * Rôles autorisés : admin, agent (propriétaire du bien).
     *
     * @authenticated
     * @urlParam property integer required ID du bien. Example: 5
     * @bodyParam images file[] required Tableau de fichiers image (JPEG, PNG, WebP, max 5Mo chacun).
     *
     * @response 201 {
     *   "message": "2 image(s) uploadée(s) avec succès.",
     *   "images": [
     *     { "id": 8, "url": "http://localhost/storage/properties/5/photo1.jpg", "is_cover": true, "sort_order": 1 },
     *     { "id": 9, "url": "http://localhost/storage/properties/5/photo2.jpg", "is_cover": false, "sort_order": 2 }
     *   ]
     * }
     * @response 403 { "message": "Action non autorisée sur ce bien." }
     * @response 422 { "message": "Les données fournies sont invalides.", "errors": { "images.0": ["Les formats acceptés sont : JPEG, PNG, WebP."] } }
     */    public function store(UploadImageRequest $request, Property $property): JsonResponse
    {
        $this->authorize('manageImages', $property);

        $images = $this->imageService->upload(
            $property,
            $request->file('images'),
            $request->user(),
        );

        return response()->json([
            'message' => count($images) . ' image(s) uploadée(s) avec succès.',
            'images'  => ImageResource::collection(collect($images)),
        ], 201);
    }

    // ─── DELETE /api/images/{image} ───────────────────────────────────────────
    /**
     * Supprimer une image
     *
     * Supprime définitivement une image et son fichier physique du disque.
     * Rôles autorisés : admin, agent (propriétaire du bien).
     *
     * @authenticated
     * @urlParam image integer required ID de l'image. Example: 8
     *
     * @response 200 { "message": "Image supprimée avec succès." }
     * @response 403 { "message": "Action non autorisée sur ce bien." }
     * @response 404 { "message": "Ressource introuvable." }
     */    public function destroy(Request $request, Image $image): JsonResponse
    {
        $this->authorize('manageImages', $image->property);

        $this->imageService->delete($image, $request->user());

        return response()->json(['message' => 'Image supprimée avec succès.']);
    }

    // ─── PATCH /api/images/{image}/cover ──────────────────────────────────────
    /**
     * Définir comme couverture
     *
     * Définit une image comme photo principale (couverture) du bien.
     * L'ancienne couverture perd automatiquement ce statut.
     * Rôles autorisés : admin, agent (propriétaire du bien).
     *
     * @authenticated
     * @urlParam image integer required ID de l'image. Example: 9
     *
     * @response 200 { "message": "Image définie comme couverture.", "image": { "id": 9, "is_cover": true } }
     * @response 403 { "message": "Action non autorisée sur ce bien." }
     * @response 404 { "message": "Ressource introuvable." }
     */    public function setCover(Request $request, Image $image): JsonResponse
    {
        $this->authorize('manageImages', $image->property);

        $updated = $this->imageService->setCover($image, $request->user());

        return response()->json([
            'message' => 'Image définie comme couverture.',
            'image'   => new ImageResource($updated),
        ]);
    }
}
