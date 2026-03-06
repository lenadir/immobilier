@extends('layouts.app')
@section('title', 'Utilisateurs')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <h5 class="mb-0 fw-semibold">Utilisateurs</h5>
</div>

{{-- ── Filters ──────────────────────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <form action="{{ route('users.index') }}" method="GET"
              class="row g-2 align-items-end">
            <div class="col-sm-6 col-lg-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Nom, email…" value="{{ request('q') }}">
            </div>
            <div class="col-sm-3 col-lg-2">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Tous les rôles</option>
                    <option value="admin"  @selected(request('role') === 'admin')>Admin</option>
                    <option value="agent"  @selected(request('role') === 'agent')>Agent</option>
                    <option value="guest"  @selected(request('role') === 'guest')>Visiteur</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i>
                </button>
                @if(request()->hasAny(['q','role']))
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
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
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Biens</th>
                    <th>Actif</th>
                    <th>Inscrit le</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="text-muted small">{{ $user->id }}</td>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td class="text-muted small">{{ $user->email }}</td>
                    <td>
                        <span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                    </td>
                    <td>{{ $user->properties_count }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge bg-success bg-opacity-10 text-success">Actif</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger">Inactif</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('users.show', $user) }}"
                           class="btn btn-sm btn-outline-secondary py-0 me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('users.edit', $user) }}"
                           class="btn btn-sm btn-outline-primary py-0 me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Supprimer cet utilisateur ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger py-0">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Aucun utilisateur trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span class="text-muted small">
            {{ $users->firstItem() }}–{{ $users->lastItem() }} / {{ $users->total() }}
        </span>
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>

@endsection
