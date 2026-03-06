<?php

namespace App\Repositories;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\FilterPropertiesDTO;
use App\DTOs\UpdatePropertyDTO;
use App\Models\Property;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PropertyRepository implements PropertyRepositoryInterface
{
    // ─── Lecture ─────────────────────────────────────────────────────────────

    public function paginate(FilterPropertiesDTO $filters): LengthAwarePaginator
    {
        $query = Property::with(['images', 'user'])
            ->published()
            ->byCity($filters->city)
            ->byType($filters->type)
            ->byStatus($filters->status)
            ->byPriceRange($filters->minPrice, $filters->maxPrice)
            ->search($filters->search)
            ->orderBy($filters->sortBy, $filters->sortDir);

        return $query->paginate($filters->perPage);
    }

    public function findById(int $id): ?Property
    {
        return Property::with(['images', 'user'])->find($id);
    }

    public function findByAgent(int $userId, FilterPropertiesDTO $filters): LengthAwarePaginator
    {
        $query = Property::with(['images'])
            ->where('user_id', $userId)
            ->byCity($filters->city)
            ->byType($filters->type)
            ->byStatus($filters->status)
            ->byPriceRange($filters->minPrice, $filters->maxPrice)
            ->search($filters->search)
            ->orderBy($filters->sortBy, $filters->sortDir);

        return $query->paginate($filters->perPage);
    }

    // ─── Écriture ────────────────────────────────────────────────────────────

    public function create(CreatePropertyDTO $dto): Property
    {
        // Le modèle génère automatiquement le titre via booted()
        return Property::create($dto->toArray());
    }

    public function update(Property $property, UpdatePropertyDTO $dto): Property
    {
        $property->update($dto->toArray());
        $property->refresh();

        return $property;
    }

    public function delete(Property $property): void
    {
        $property->delete();
    }

    // ─── Corbeille (soft delete) ──────────────────────────────────────────────

    public function findTrashedById(int $id): ?Property
    {
        return Property::onlyTrashed()->find($id);
    }

    public function trashed(int $perPage = 15): LengthAwarePaginator
    {
        return Property::onlyTrashed()
            ->with(['user'])
            ->orderByDesc('deleted_at')
            ->paginate($perPage);
    }

    public function restore(Property $property): Property
    {
        $property->restore();
        $property->refresh();

        return $property;
    }

    public function forceDelete(Property $property): void
    {
        $property->forceDelete();
    }
}
