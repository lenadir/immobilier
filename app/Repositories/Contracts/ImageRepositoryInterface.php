<?php

namespace App\Repositories\Contracts;

use App\Models\Image;
use App\Models\Property;
use Illuminate\Http\UploadedFile;

interface ImageRepositoryInterface
{
    /**
     * Stocke le fichier et crée l'enregistrement Image.
     */
    public function store(Property $property, UploadedFile $file, bool $isCover = false): Image;

    /**
     * Supprime l'image (fichier + enregistrement DB).
     */
    public function delete(Image $image): void;

    /**
     * Définit une image comme couverture du bien.
     */
    public function setCover(Image $image): Image;
}
