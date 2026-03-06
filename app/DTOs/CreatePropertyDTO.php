<?php

namespace App\DTOs;

/**
 * DTO de création d'un bien immobilier.
 * Transmis du Controller → Service → Repository.
 */
final class CreatePropertyDTO
{
    public function __construct(
        public readonly int     $userId,
        public readonly string  $type,
        public readonly float   $price,
        public readonly string  $city,
        public readonly string  $status,
        public readonly ?int    $rooms       = null,
        public readonly ?float  $surface     = null,
        public readonly ?string $address     = null,
        public readonly ?string $description = null,
        public readonly bool    $isPublished = false,
    ) {}

    /**
     * Construit le DTO à partir d'un array validé (Form Request).
     */
    public static function fromArray(int $userId, array $data): self
    {
        return new self(
            userId:      $userId,
            type:        $data['type'],
            price:       (float) $data['price'],
            city:        $data['city'],
            status:      $data['status']      ?? 'disponible',
            rooms:       isset($data['rooms'])   ? (int) $data['rooms']   : null,
            surface:     isset($data['surface']) ? (float) $data['surface'] : null,
            address:     $data['address']     ?? null,
            description: $data['description'] ?? null,
            isPublished: (bool) ($data['is_published'] ?? false),
        );
    }

    /**
     * Convertit le DTO en tableau pour Eloquent::create().
     */
    public function toArray(): array
    {
        return [
            'user_id'      => $this->userId,
            'type'         => $this->type,
            'price'        => $this->price,
            'city'         => $this->city,
            'status'       => $this->status,
            'rooms'        => $this->rooms,
            'surface'      => $this->surface,
            'address'      => $this->address,
            'description'  => $this->description,
            'is_published' => $this->isPublished,
        ];
    }
}
