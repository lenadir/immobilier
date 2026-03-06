<?php

namespace App\DTOs;

/**
 * DTO de mise à jour partielle d'un bien immobilier.
 * Seuls les champs présents dans la requête sont mis à jour (PATCH).
 */
final class UpdatePropertyDTO
{
    public function __construct(
        public readonly ?string $type        = null,
        public readonly ?float  $price       = null,
        public readonly ?string $city        = null,
        public readonly ?string $status      = null,
        public readonly ?int    $rooms       = null,
        public readonly ?float  $surface     = null,
        public readonly ?string $address     = null,
        public readonly ?string $description = null,
        public readonly ?bool   $isPublished = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type:        $data['type']         ?? null,
            price:       isset($data['price'])       ? (float) $data['price']       : null,
            city:        $data['city']         ?? null,
            status:      $data['status']       ?? null,
            rooms:       isset($data['rooms'])       ? (int) $data['rooms']         : null,
            surface:     isset($data['surface'])     ? (float) $data['surface']     : null,
            address:     $data['address']      ?? null,
            description: $data['description']  ?? null,
            isPublished: isset($data['is_published']) ? (bool) $data['is_published'] : null,
        );
    }

    /**
     * Retourne uniquement les champs non-null (mise à jour partielle).
     */
    public function toArray(): array
    {
        return array_filter([
            'type'         => $this->type,
            'price'        => $this->price,
            'city'         => $this->city,
            'status'       => $this->status,
            'rooms'        => $this->rooms,
            'surface'      => $this->surface,
            'address'      => $this->address,
            'description'  => $this->description,
            'is_published' => $this->isPublished,
        ], fn ($value) => $value !== null);
    }
}
