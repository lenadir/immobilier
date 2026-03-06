@extends('layouts.app')
@section('title', 'Modifier : ' . $user->name)

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Modifier l'utilisateur</h5>
</div>

<div class="card" style="max-width:560px">
    <div class="card-body">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf @method('PUT')

            <div class="row g-3">

                {{-- Name --}}
                <div class="col-12">
                    <label class="form-label fw-semibold" for="name">
                        Nom <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Email --}}
                <div class="col-12">
                    <label class="form-label fw-semibold" for="email">
                        Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Phone --}}
                <div class="col-12">
                    <label class="form-label fw-semibold" for="phone">Téléphone</label>
                    <input type="text" id="phone" name="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $user->phone) }}">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Role --}}
                <div class="col-sm-6">
                    <label class="form-label fw-semibold" for="role">Rôle</label>
                    <select id="role" name="role"
                            class="form-select @error('role') is-invalid @enderror">
                        <option value="guest" @selected(old('role', $user->role) === 'guest')>Visiteur</option>
                        <option value="agent" @selected(old('role', $user->role) === 'agent')>Agent</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Is active --}}
                <div class="col-sm-6 d-flex align-items-end">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="is_active"
                               name="is_active" value="1"
                               @checked(old('is_active', $user->is_active))>
                        <label class="form-check-label fw-semibold" for="is_active">
                            Compte actif
                        </label>
                    </div>
                </div>

                {{-- New password (optional) --}}
                <div class="col-12">
                    <label class="form-label fw-semibold" for="password">
                        Nouveau mot de passe
                        <span class="text-muted fw-normal small">(laisser vide pour ne pas changer)</span>
                    </label>
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold" for="password_confirmation">
                        Confirmer le mot de passe
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="form-control" autocomplete="new-password">
                </div>

            </div>

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
