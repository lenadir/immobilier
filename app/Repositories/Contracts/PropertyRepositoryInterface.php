<?php

namespace App\Repositories\Contracts;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\FilterPropertiesDTO;
use App\DTOs\UpdatePropertyDTO;
use App\Models\Property;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PropertyRepositoryInterface
{
    /**
     * Retourne une liste paginée de biens selon les filtres.
     */
    public function paginate(FilterPropertiesDTO $filters): LengthAwarePaginator;

    /**
     * Trouve un bien par son ID (avec ses images).
     */
    public function findById(int $id): ?Property;

    /**
     * Crée un nouveau bien à partir du DTO.
     */
    public function create(CreatePropertyDTO $dto): Property;

    /**
     * Met à jour un bien existant à partir du DTO.
     */
    public function update(Property $property, UpdatePropertyDTO $dto): Property;

    /**
     * Supprime un bien (soft delete).
     */
    public function delete(Property $property): void;

    /**
     * Retourne tous les biens d'un agent.
     */
    public function findByAgent(int $userId, FilterPropertiesDTO $filters): LengthAwarePaginator;

    /**
     * Trouve un bien dans la corbeille par son ID.
     */
    public function findTrashedById(int $id): ?Property;

    /**
     * Retourne la corbeille (biens soft-deletés) — admin uniquement.
     */
    public function trashed(int $perPage = 15): LengthAwarePaginator;

    /**
     * Restaure un bien soft-deleté.
     */
    public function restore(Property $property): Property;

    /**
     * Suppression définitive (force delete).
     */
    public function forceDelete(Property $property): void;
}
