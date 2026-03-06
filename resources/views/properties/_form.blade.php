{{-- Shared form fields — used by create & edit --}}
@php $p = $property; @endphp

<div class="row g-3">

    {{-- Title --}}
    <div class="col-12">
        <label class="form-label fw-semibold" for="title">Titre <span class="text-danger">*</span></label>
        <input type="text" id="title" name="title"
               class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $p?->title) }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Type + Status --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold" for="type">Type <span class="text-danger">*</span></label>
        <select id="type" name="type"
                class="form-select @error('type') is-invalid @enderror" required>
            <option value="">— Choisir —</option>
            @foreach(\App\Models\Property::TYPES as $type)
                <option value="{{ $type }}" @selected(old('type', $p?->type) === $type)>
                    {{ ucfirst($type) }}
                </option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-6">
        <label class="form-label fw-semibold" for="status">Statut <span class="text-danger">*</span></label>
        <select id="status" name="status"
                class="form-select @error('status') is-invalid @enderror" required>
            @foreach(\App\Models\Property::STATUSES as $status)
                <option value="{{ $status }}" @selected(old('status', $p?->status) === $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Price + Surface --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold" for="price">Prix (DA) <span class="text-danger">*</span></label>
        <input type="number" id="price" name="price" min="0" step="1"
               class="form-control @error('price') is-invalid @enderror"
               value="{{ old('price', $p?->price) }}" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-6">
        <label class="form-label fw-semibold" for="surface">Surface (m²)</label>
        <input type="number" id="surface" name="surface" min="0" step="0.01"
               class="form-control @error('surface') is-invalid @enderror"
               value="{{ old('surface', $p?->surface) }}">
        @error('surface')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- City + Address --}}
    <div class="col-sm-4">
        <label class="form-label fw-semibold" for="city">Ville</label>
        <input type="text" id="city" name="city"
               class="form-control @error('city') is-invalid @enderror"
               value="{{ old('city', $p?->city) }}">
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-sm-8">
        <label class="form-label fw-semibold" for="address">Adresse</label>
        <input type="text" id="address" name="address"
               class="form-control @error('address') is-invalid @enderror"
               value="{{ old('address', $p?->address) }}">
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Rooms --}}
    <div class="col-sm-6">
        <label class="form-label fw-semibold" for="rooms">Nombre de pièces</label>
        <input type="number" id="rooms" name="rooms" min="0"
               class="form-control @error('rooms') is-invalid @enderror"
               value="{{ old('rooms', $p?->rooms) }}">
        @error('rooms')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Description --}}
    <div class="col-12">
        <label class="form-label fw-semibold" for="description">Description</label>
        <textarea id="description" name="description" rows="4"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $p?->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Published --}}
    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_published" name="is_published"
                   value="1" @checked(old('is_published', $p?->is_published))>
            <label class="form-check-label" for="is_published">Publier ce bien</label>
        </div>
    </div>

    {{-- ── New image upload with live preview ───────────────────────────────── --}}
    <div class="col-12">
        <label class="form-label fw-semibold" for="images">
            {{ ($p && $p->images->isNotEmpty()) ? 'Ajouter des photos' : 'Photos' }}
        </label>

        <label for="images" id="drop-zone"
               class="d-flex flex-column align-items-center justify-content-center
                      border border-2 rounded p-4 text-muted"
               style="cursor:pointer;border-style:dashed!important;border-color:#cbd5e1!important;
                      min-height:110px;transition:border-color .2s,background .2s">
            <i class="bi bi-cloud-arrow-up fs-3 mb-1"></i>
            <span class="small fw-semibold">Cliquer ou glisser-déposer des images ici</span>
            <span class="small">JPG, PNG, WEBP — max 5 Mo par fichier</span>
        </label>

        <input type="file" id="images" name="images[]" multiple accept="image/*"
               class="d-none @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror">
        @error('images')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        @error('images.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

        {{-- Live preview --}}
        <div id="new-preview" class="row g-2 mt-2"></div>
    </div>

</div>

{{-- ── JS live preview ─────────────────────────────────────────────────────── --}}
<script>
(function () {
    const input    = document.getElementById('images');
    const dropZone = document.getElementById('drop-zone');
    const preview  = document.getElementById('new-preview');

    function renderPreviews(files) {
        preview.innerHTML = '';
        Array.from(files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const col = document.createElement('div');
                col.className = 'col-6 col-sm-4 col-md-3';
                const name = file.name.length > 20 ? file.name.substring(0, 18) + '…' : file.name;
                const coverBadge = i === 0
                    ? `<span class="position-absolute top-0 start-0 badge bg-primary m-1" style="font-size:.65rem"><i class="bi bi-star-fill me-1"></i>Couverture</span>`
                    : '';
                col.innerHTML = `
                    <div class="position-relative rounded overflow-hidden border" style="aspect-ratio:4/3">
                        <img src="${e.target.result}" class="w-100 h-100" style="object-fit:cover" alt="">
                        ${coverBadge}
                        <div class="position-absolute bottom-0 start-0 end-0 px-2 py-1"
                             style="background:rgba(0,0,0,.45);font-size:.7rem;color:#fff;
                                    white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            ${name}
                        </div>
                    </div>`;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }

    input.addEventListener('change', () => renderPreviews(input.files));

    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.style.background = '#f0f9ff';
        dropZone.style.borderColor = '#3b82f6';
    });
    dropZone.addEventListener('dragleave', () => {
        dropZone.style.background = '';
        dropZone.style.borderColor = '';
    });
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.style.background = '';
        dropZone.style.borderColor = '';
        const dt = new DataTransfer();
        Array.from(e.dataTransfer.files)
            .filter(f => f.type.startsWith('image/'))
            .forEach(f => dt.items.add(f));
        input.files = dt.files;
        renderPreviews(input.files);
    });
})();
</script>
