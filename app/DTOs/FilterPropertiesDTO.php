<?php

namespace App\DTOs;

/**
 * DTO de filtrage / recherche de biens immobiliers.
 * Transmis du Controller → Service → Repository pour construire la query.
 */
final class FilterPropertiesDTO
{
    public function __construct(
        public readonly ?string $city     = null,
        public readonly ?string $type     = null,
        public readonly ?float  $minPrice = null,
        public readonly ?float  $maxPrice = null,
        public readonly ?string $status   = null,
        public readonly ?string $search   = null,   // full-text sur title/description
        public readonly int     $perPage  = 15,
        public readonly string  $sortBy   = 'created_at',
        public readonly string  $sortDir  = 'desc',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            city:     $data['city']      ?? null,
            type:     $data['type']      ?? null,
            minPrice: isset($data['min_price']) ? (float) $data['min_price'] : null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            status:   $data['status']    ?? null,
            search:   $data['search']    ?? null,
            perPage:  (int) ($data['per_page'] ?? 15),
            sortBy:   $data['sort_by']   ?? 'created_at',
            sortDir:  $data['sort_dir']  ?? 'desc',
        );
    }
}
