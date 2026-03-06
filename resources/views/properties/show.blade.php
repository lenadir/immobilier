@extends('layouts.app')
@section('title', $property->title)

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('properties.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">{{ $property->title }}</h5>
    <span class="badge badge-{{ $property->status }}">{{ ucfirst($property->status) }}</span>
    <div class="ms-auto d-flex gap-2">
        @can('update', $property)
        <a href="{{ route('properties.edit', $property) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        @endcan
        @can('delete', $property)
        <form action="{{ route('properties.destroy', $property) }}" method="POST"
              onsubmit="return confirm('Supprimer ce bien ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Supprimer
            </button>
        </form>
        @endcan
    </div>
</div>

<div class="row g-4">

    {{-- ── Main details ─────────────────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header fw-semibold small">Informations générales</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Type</dt>
                    <dd class="col-sm-8 text-capitalize">{{ $property->type }}</dd>

                    <dt class="col-sm-4">Prix</dt>
                    <dd class="col-sm-8 fw-bold">{{ number_format($property->price, 0, ',', ' ') }} DA</dd>

                    <dt class="col-sm-4">Surface</dt>
                    <dd class="col-sm-8">{{ $property->surface ? $property->surface . ' m²' : '—' }}</dd>

                    <dt class="col-sm-4">Ville</dt>
                    <dd class="col-sm-8">{{ ucfirst($property->city ?? '—') }}</dd>

                    <dt class="col-sm-4">Adresse</dt>
                    <dd class="col-sm-8">{{ $property->address ?? '—' }}</dd>

                    <dt class="col-sm-4">Pièces</dt>
                    <dd class="col-sm-8">{{ $property->rooms ?? '—' }}</dd>

                    <dt class="col-sm-4">Publié</dt>
                    <dd class="col-sm-8">{{ $property->is_published ? 'Oui' : 'Non' }}</dd>

                    <dt class="col-sm-4">Créé le</dt>
                    <dd class="col-sm-8">{{ $property->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
        </div>

        @if($property->description)
        <div class="card mb-4">
            <div class="card-header fw-semibold small">Description</div>
            <div class="card-body">
                <p class="mb-0" style="white-space:pre-line">{{ $property->description }}</p>
            </div>
        </div>
        @endif

        {{-- Images --}}
        @if($property->images->isNotEmpty())
        @php
            $cover  = $property->images->firstWhere('is_cover', true) ?? $property->images->first();
            $thumbs = $property->images->where('id', '!=', $cover->id)->values();
        @endphp
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-semibold small">
                    Photos ({{ $property->images->count() }})
                </span>
                @can('update', $property)
                <a href="{{ route('properties.edit', $property) }}" class="btn btn-sm btn-outline-primary py-0">
                    <i class="bi bi-pencil me-1"></i>Gérer les photos
                </a>
                @endcan
            </div>
            <div class="card-body p-2">

                {{-- Cover --}}
                <a href="{{ $cover->url }}" target="_blank" class="d-block mb-2 rounded overflow-hidden"
                   style="aspect-ratio:16/9">
                    <img src="{{ $cover->url }}" alt="Photo principale"
                         class="w-100 h-100" style="object-fit:cover">
                </a>

                {{-- Thumbnails --}}
                @if($thumbs->isNotEmpty())
                <div class="row g-1">
                    @foreach($thumbs as $img)
                    <div class="col-3">
                        <a href="{{ $img->url }}" target="_blank"
                           class="d-block rounded overflow-hidden"
                           style="aspect-ratio:1/1">
                            <img src="{{ $img->url }}" alt=""
                                 class="w-100 h-100" style="object-fit:cover">
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif

            </div>
        </div>
        @endif
    </div>

    {{-- ── Sidebar ──────────────────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-semibold small">Agent responsable</div>
            <div class="card-body text-center">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary
                            mx-auto d-flex align-items-center justify-content-center
                            mb-3 fw-bold fs-4"
                     style="width:64px;height:64px">
                    {{ strtoupper(substr($property->user?->name ?? '?', 0, 1)) }}
                </div>
                <p class="fw-semibold mb-1">{{ $property->user?->name ?? 'Non assigné' }}</p>
                <p class="text-muted small mb-2">{{ $property->user?->email ?? '—' }}</p>
                @if($property->user)
                <span class="badge badge-{{ $property->user->role }}">
                    {{ ucfirst($property->user->role) }}
                </span>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection
