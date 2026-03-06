@extends('layouts.app')
@section('title', 'Nouveau bien')

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('properties.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Nouveau bien</h5>
</div>

<div class="card" style="max-width:900px">
    <div class="card-body">
        <form action="{{ route('properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('properties._form', ['property' => null])

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
