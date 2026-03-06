@extends('layouts.app')
@section('title', 'Biens immobiliers')

@section('content')

{{-- ── Toolbar ──────────────────────────────────────────────────────────────── --}}
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-semibold">Biens immobiliers</h5>
    @can('create', App\Models\Property::class)
    <a href="{{ route('properties.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nouveau bien
    </a>
    @endcan
</div>

{{-- ── Filters ──────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form action="{{ route('properties.index') }}" method="GET"
              class="row g-2 align-items-end">
            <div class="col-sm-5 col-lg-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Rechercher…" value="{{ request('search') }}">
            </div>
            <div class="col-sm-3 col-lg-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous les types</option>
                    @foreach(\App\Models\Property::TYPES as $type)
                        <option value="{{ $type }}" @selected(request('type') === $type)>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3 col-lg-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous les statuts</option>
                    @foreach(\App\Models\Property::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i>
                </button>
                @if(request()->hasAny(['search','type','status']))
                <a href="{{ route('properties.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-x-lg"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- ── Table ────────────────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th></th>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Ville</th>
                    <th>Prix (DA)</th>
                    <th>Statut</th>
                    @if(auth()->user()->isAdmin())
                    <th>Agent</th>
                    @endif
                    <th>Ajouté le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                <tr>
                    <td class="text-muted small">{{ $property->id }}</td>
                    <td style="width:60px">
                        @php $cover = $property->images->first(); @endphp
                        @if($cover)
                            <img src="{{ $cover->url }}" alt=""
                                 style="width:52px;height:40px;object-fit:cover;border-radius:6px">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-light rounded"
                                 style="width:52px;height:40px">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $property->title }}</td>
                    <td class="text-capitalize text-muted small">{{ $property->type }}</td>
                    <td>{{ ucfirst($property->city) }}</td>
                    <td>{{ number_format($property->price, 0, ',', ' ') }}</td>
                    <td>
                        <span class="badge badge-{{ $property->status }}">
                            {{ ucfirst($property->status) }}
                        </span>
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="text-muted small">{{ $property->user?->name ?? '—' }}</td>
                    @endif
                    <td class="text-muted small">{{ $property->created_at->format('d/m/Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('properties.show', $property) }}"
                           class="btn btn-sm btn-outline-secondary py-0 me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        @can('update', $property)
                        <a href="{{ route('properties.edit', $property) }}"
                           class="btn btn-sm btn-outline-primary py-0 me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                        @can('delete', $property)
                        <form action="{{ route('properties.destroy', $property) }}" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Supprimer ce bien ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger py-0">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Aucun bien trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($properties->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span class="text-muted small">
            {{ $properties->firstItem() }}–{{ $properties->lastItem() }} / {{ $properties->total() }}
        </span>
        {{ $properties->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
