<?php

namespace App\Repositories;

use App\Models\Image;
use App\Models\Property;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageRepository implements ImageRepositoryInterface
{
    public function store(Property $property, UploadedFile $file, bool $isCover = false): Image
    {
        // Stocke dans storage/app/public/properties/{id}/
        $path = $file->store("properties/{$property->id}", 'public');

        // Si première image ou isCover demandé, on marque les autres comme non-cover
        if ($isCover) {
            $property->images()->update(['is_cover' => false]);
        }

        $sortOrder = $property->images()->max('sort_order') + 1;

        return $property->images()->create([
            'path'          => $path,
            'disk'          => 'public',
            'original_name' => $file->getClientOriginalName(),
            'size'          => $file->getSize(),
            'mime_type'     => $file->getMimeType(),
            'is_cover'      => $isCover,
            'sort_order'    => $sortOrder,
        ]);
    }

    public function delete(Image $image): void
    {
        Storage::disk($image->disk)->delete($image->path);
        $image->delete();
    }

    public function setCover(Image $image): Image
    {
        // Retire la couverture de toutes les images du bien
        Image::where('property_id', $image->property_id)
            ->update(['is_cover' => false]);

        $image->update(['is_cover' => true]);
        $image->refresh();

        return $image;
    }
}
