<?php

namespace App\Services;

use App\Models\Image;
use App\Models\Property;
use App\Models\User;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;

class ImageService
{
    public function __construct(
        private readonly ImageRepositoryInterface $imageRepository,
    ) {}

    /**
     * Upload une ou plusieurs images pour un bien.
     *
     * @param  UploadedFile[]  $files
     * @return Image[]
     * @throws AuthorizationException
     */
    public function upload(Property $property, array $files, User $authUser): array
    {
        $this->authorizeAction($property, $authUser);

        $images = [];
        foreach ($files as $index => $file) {
            $isCover = ($index === 0 && $property->images()->count() === 0);
            $images[] = $this->imageRepository->store($property, $file, $isCover);
        }

        return $images;
    }

    /**
     * Supprime une image d'un bien.
     *
     * @throws AuthorizationException
     */
    public function delete(Image $image, User $authUser): void
    {
        $this->authorizeAction($image->property, $authUser);

        $this->imageRepository->delete($image);
    }

    /**
     * Définit une image comme photo de couverture.
     *
     * @throws AuthorizationException
     */
    public function setCover(Image $image, User $authUser): Image
    {
        $this->authorizeAction($image->property, $authUser);

        return $this->imageRepository->setCover($image);
    }

    // ─── Autorisation ─────────────────────────────────────────────────────────

    /**
     * @throws AuthorizationException
     */
    private function authorizeAction(Property $property, User $user): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isAgent() && $property->user_id === $user->id) {
            return;
        }

        throw new AuthorizationException("Action non autorisée sur ce bien.");
    }
}
