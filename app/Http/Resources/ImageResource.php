<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Image
 */
class ImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'url'           => $this->url,              // Accesseur du modèle
            'original_name' => $this->original_name,
            'size'          => $this->size,
            'mime_type'     => $this->mime_type,
            'is_cover'      => $this->is_cover,
            'sort_order'    => $this->sort_order,
            'created_at'    => $this->created_at?->toIso8601String(),
        ];
    }
}
