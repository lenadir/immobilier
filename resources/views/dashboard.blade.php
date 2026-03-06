@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

@unless(auth()->user()->isGuest())
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff">
                <i class="bi bi-house-door text-primary"></i>
            </div>
            <div class="stat-value">{{ $stats['total_properties'] }}</div>
            <div class="stat-label">Biens au total</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4">
                <i class="bi bi-check-circle text-success"></i>
            </div>
            <div class="stat-value">{{ $stats['published'] }}</div>
            <div class="stat-label">Biens publiés</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdf4ff">
                <i class="bi bi-people text-purple" style="color:#9333ea"></i>
            </div>
            <div class="stat-value">{{ $stats['total_agents'] }}</div>
            <div class="stat-label">Agents immobiliers</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff7ed">
                <i class="bi bi-trash" style="color:#ea580c"></i>
            </div>
            <div class="stat-value">{{ $stats['trashed'] }}</div>
            <div class="stat-label">Biens en corbeille</div>
        </div>
    </div>
</div>
@endunless


<div class="row g-3 mb-4">
    {{-- ── Status breakdown ──────────────────────────────────────────────────── --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart text-primary"></i>
                <span class="fw-semibold small">Par statut</span>
            </div>
            <div class="card-body">
                @php
                    $statusData = [
                        'disponible' => [$stats['disponible'], '#16a34a', 'bi-circle-fill'],
                        'vendu'      => [$stats['vendu'],      '#dc2626', 'bi-circle-fill'],
                        'location'   => [$stats['location'],   '#2563eb', 'bi-circle-fill'],
                    ];
                    $total = max($stats['total_properties'], 1);
                @endphp
                @foreach($statusData as $label => [$count, $color, $icon])
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold text-capitalize">{{ $label }}</span>
                        <span class="small text-muted">{{ $count }}</span>
                    </div>
                    <div class="progress" style="height:6px">
                        <div class="progress-bar" role="progressbar"
                            style="width:{{ round($count / $total * 100) }}%; background:{{ $color }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── By type breakdown ─────────────────────────────────────────────────── --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart text-primary"></i>
                <span class="fw-semibold small">Par type</span>
            </div>
            <div class="card-body">
                @forelse($byType as $type => $count)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small text-capitalize">{{ $type }}</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $count }}</span>
                </div>
                @empty
                    <p class="text-muted small mb-0">Aucune donnée.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Users summary ────────────────────────────────────────────────────── --}}
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-person-badge text-primary"></i>
                <span class="fw-semibold small">Utilisateurs</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small">Total</span>
                    <span class="fw-bold">{{ $stats['total_users'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small">Agents</span>
                    <span class="fw-bold text-primary">{{ $stats['total_agents'] }}</span>
                </div>
                @if(auth()->user()->isAdmin())
                <div class="mt-3">
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        Gérer les utilisateurs
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Recent properties ────────────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-primary"></i>
            <span class="fw-semibold small">Derniers biens ajoutés</span>
        </div>
        <a href="{{ route('properties.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Ville</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Agent</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $property)
                <tr>
                    <td class="fw-semibold">{{ $property->title }}</td>
                    <td class="text-capitalize text-muted">{{ $property->type }}</td>
                    <td>{{ ucfirst($property->city) }}</td>
                    <td>{{ number_format($property->price, 0, ',', ' ') }} DA</td>
                    <td>
                        <span class="badge badge-{{ $property->status }}">{{ ucfirst($property->status) }}</span>
                    </td>
                    <td class="text-muted small">{{ $property->user?->name ?? '—' }}</td>
                    <td>
                        <a href="{{ route('properties.show', $property) }}" class="btn btn-sm btn-outline-secondary py-0">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Aucun bien pour l'instant.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
