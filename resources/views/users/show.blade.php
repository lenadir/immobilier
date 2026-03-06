@extends('layouts.app')
@section('title', $user->name)

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">{{ $user->name }}</h5>
    <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
    <div class="ms-auto d-flex gap-2">
        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        @if($user->id !== auth()->id())
        <form action="{{ route('users.destroy', $user) }}" method="POST"
              onsubmit="return confirm('Supprimer cet utilisateur ?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Supprimer
            </button>
        </form>
        @endif
    </div>
</div>

<div class="row g-4">

    {{-- ── Profile card ─────────────────────────────────────────────────────── --}}
    <div class="col-lg-4">
        <div class="card text-center mb-4">
            <div class="card-body py-4">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary
                            mx-auto d-flex align-items-center justify-content-center
                            fw-bold fs-3 mb-3"
                     style="width:80px;height:80px">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h6 class="fw-bold mb-1">{{ $user->name }}</h6>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                @if($user->phone)
                <p class="text-muted small mb-2">
                    <i class="bi bi-telephone me-1"></i>{{ $user->phone }}
                </p>
                @endif
                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }} bg-opacity-10
                             {{ $user->is_active ? 'text-success' : 'text-danger' }} me-1">
                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                </span>
                <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
            </div>
            <div class="card-footer text-muted small">
                Inscrit le {{ $user->created_at->format('d/m/Y') }}
            </div>
        </div>

        <div class="card">
            <div class="card-body text-center">
                <p class="display-6 fw-bold mb-0">{{ $user->properties_count }}</p>
                <p class="text-muted small mb-0">Biens publiés</p>
            </div>
        </div>
    </div>

    {{-- ── Recent properties ────────────────────────────────────────────────── --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-semibold small">Derniers biens</div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Prix (DA)</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentProperties as $property)
                        <tr>
                            <td class="fw-semibold">{{ $property->title }}</td>
                            <td class="text-capitalize text-muted small">{{ $property->type }}</td>
                            <td>{{ number_format($property->price, 0, ',', ' ') }}</td>
                            <td>
                                <span class="badge badge-{{ $property->status }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('properties.show', $property) }}"
                                   class="btn btn-sm btn-outline-secondary py-0">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Aucun bien pour cet utilisateur.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentProperties->isNotEmpty())
            <div class="card-footer">
                <a href="{{ route('properties.index') }}?user_id={{ $user->id }}"
                   class="btn btn-sm btn-outline-primary">Voir tous les biens</a>
            </div>
            @endif
        </div>
    </div>

</div>

@endsection
