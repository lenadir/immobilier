<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Property
 */
class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'type'         => $this->type,
            'rooms'        => $this->rooms,
            'surface'      => $this->surface,
            'price'        => $this->price,
            'city'         => $this->city,
            'address'      => $this->address,
            'description'  => $this->description,
            'status'       => $this->status,
            'is_published' => $this->is_published,
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),

            // Relations (chargées selon le contexte)
            'agent'  => new UserResource($this->whenLoaded('user')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
