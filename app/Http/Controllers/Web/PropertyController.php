<?php

namespace App\Http\Controllers\Web;

use App\DTOs\CreatePropertyDTO;
use App\DTOs\UpdatePropertyDTO;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Property;
use App\Services\ImageService;
use App\Services\PropertyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertyService $propertyService,
        private readonly ImageService   $imageService,
    ) {}

    public function index(Request $request)
    {
        $query = Property::with(['user', 'images'])->latest();

        // Agents only see their own properties
        if (Auth::user()->isAgent()) {
            $query->where('user_id', Auth::id());
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $properties = $query->paginate(12)->withQueryString();

        return view('properties.index', [
            'properties' => $properties,
            'types'      => Property::TYPES,
            'statuses'   => Property::STATUSES,
            'filters'    => $request->only('search', 'type', 'status'),
        ]);
    }

    public function show(Property $property)
    {
        $property->load(['user', 'images']);

        return view('properties.show', compact('property'));
    }

    public function create()
    {
        $this->authorizeCreateOrEdit();

        return view('properties.create', [
            'types'    => Property::TYPES,
            'statuses' => Property::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeCreateOrEdit();

        $data = $request->validate([
            'type'         => ['required', 'in:' . implode(',', Property::TYPES)],
            'price'        => ['required', 'numeric', 'min:0'],
            'city'         => ['required', 'string', 'max:100'],
            'status'       => ['required', 'in:' . implode(',', Property::STATUSES)],
            'rooms'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'surface'      => ['nullable', 'numeric', 'min:1'],
            'address'      => ['nullable', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
            'images'       => ['nullable', 'array', 'max:10'],
            'images.*'     => ['image', 'max:5120'],
        ]);

        $dto = CreatePropertyDTO::fromArray(Auth::id(), $data);
        $property = $this->propertyService->create($dto);

        if (!empty($data['images'])) {
            $this->imageService->upload($property, $data['images'], Auth::user());
        }

        return redirect()
            ->route('properties.show', $property)
            ->with('success', "Bien « {$property->title} » créé avec succès.");
    }

    public function edit(Property $property)
    {
        $this->authorizeOwner($property);
        $property->load('images');

        return view('properties.edit', [
            'property' => $property,
            'types'    => Property::TYPES,
            'statuses' => Property::STATUSES,
        ]);
    }

    public function update(Request $request, Property $property)
    {
        $this->authorizeOwner($property);

        $data = $request->validate([
            'type'         => ['required', 'in:' . implode(',', Property::TYPES)],
            'price'        => ['required', 'numeric', 'min:0'],
            'city'         => ['required', 'string', 'max:100'],
            'status'       => ['required', 'in:' . implode(',', Property::STATUSES)],
            'rooms'        => ['nullable', 'integer', 'min:1', 'max:100'],
            'surface'      => ['nullable', 'numeric', 'min:1'],
            'address'      => ['nullable', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
            'images'       => ['nullable', 'array', 'max:10'],
            'images.*'     => ['image', 'max:5120'],
        ]);

        $dto = UpdatePropertyDTO::fromArray($data);
        $updated = $this->propertyService->update($property, $dto, Auth::user());

        if (!empty($data['images'])) {
            $this->imageService->upload($updated, $data['images'], Auth::user());
        }

        return redirect()
            ->route('properties.show', $updated)
            ->with('success', "Bien « {$updated->title} » mis à jour.");
    }

    public function destroy(Property $property)
    {
        $this->authorizeOwner($property);

        $title = $property->title;
        $this->propertyService->delete($property, Auth::user());

        return redirect()
            ->route('properties.index')
            ->with('success', "Bien « {$title} » supprimé.");
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function destroyImage(Property $property, Image $image)
    {
        $this->authorizeOwner($property);

        if ($image->property_id !== $property->id) {
            abort(404);
        }

        $this->imageService->delete($image, Auth::user());

        return back()->with('success', 'Photo supprimée.');
    }

    public function setCoverImage(Property $property, Image $image)
    {
        $this->authorizeOwner($property);

        if ($image->property_id !== $property->id) {
            abort(404);
        }

        $this->imageService->setCover($image, Auth::user());

        return back()->with('success', 'Photo de couverture mise à jour.');
    }

    private function authorizeCreateOrEdit(): void
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isAgent()) {
            abort(403, 'Accès refusé.');
        }
    }

    private function authorizeOwner(Property $property): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return;
        }
        if ($user->isAgent() && $property->user_id === $user->id) {
            return;
        }
        abort(403, "Vous n'êtes pas autorisé à modifier ce bien.");
    }
}
