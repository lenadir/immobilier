@extends('layouts.app')
@section('title', 'Modifier : ' . $property->title)

@section('content')

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('properties.show', $property) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="mb-0 fw-semibold">Modifier le bien</h5>
</div>

{{-- ── Existing images management (outside main form to avoid nesting) ──────── --}}
@if($property->images->isNotEmpty())
<div class="card mb-4" style="max-width:900px">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Photos actuelles</h6>
        <div class="row g-2">
            @foreach($property->images->sortBy('sort_order') as $image)
            <div class="col-6 col-sm-4 col-md-3">
                <div class="position-relative rounded overflow-hidden
                            {{ $image->is_cover ? 'border border-2 border-primary' : 'border' }}"
                     style="aspect-ratio:4/3">
                    <img src="{{ $image->url }}" alt=""
                         class="w-100 h-100" style="object-fit:cover">

                    @if($image->is_cover)
                    <span class="position-absolute top-0 start-0 badge bg-primary m-1"
                          style="font-size:.65rem">
                        <i class="bi bi-star-fill me-1"></i>Couverture
                    </span>
                    @endif

                    <div class="position-absolute bottom-0 start-0 end-0 d-flex gap-1 p-1"
                         style="background:rgba(0,0,0,.5)">
                        @unless($image->is_cover)
                        <form action="{{ route('properties.images.cover', [$property, $image]) }}"
                              method="POST" class="flex-fill">
                            @csrf
                            <button type="submit" title="Définir comme couverture"
                                    class="btn btn-sm btn-warning w-100 py-0"
                                    style="font-size:.7rem">
                                <i class="bi bi-star"></i>
                            </button>
                        </form>
                        @endunless
                        <form action="{{ route('properties.images.destroy', [$property, $image]) }}"
                              method="POST" class="flex-fill"
                              onsubmit="return confirm('Supprimer cette photo ?')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Supprimer"
                                    class="btn btn-sm btn-danger w-100 py-0"
                                    style="font-size:.7rem">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<div class="card" style="max-width:900px">
    <div class="card-body">
        <form action="{{ route('properties.update', $property) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            @include('properties._form', ['property' => $property])

            <div class="d-flex gap-2 justify-content-end mt-4">
                <a href="{{ route('properties.show', $property) }}" class="btn btn-outline-secondary">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
