<?php

namespace App\Http\Requests\Property;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Seuls admin et agent peuvent créer un bien
        $user = $this->user();
        return $user && ($user->isAdmin() || $user->isAgent());
    }

    public function rules(): array
    {
        return [
            'type'         => ['required', Rule::in(Property::TYPES)],
            'price'        => ['required', 'numeric', 'min:0'],
            'city'         => ['required', 'string', 'max:100'],
            'status'       => ['sometimes', Rule::in(Property::STATUSES)],
            'rooms'        => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
            'surface'      => ['sometimes', 'nullable', 'numeric', 'min:1'],
            'address'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'description'  => ['sometimes', 'nullable', 'string', 'max:5000'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'  => 'Le type de bien est obligatoire.',
            'type.in'        => 'Type de bien invalide. Valeurs acceptées : ' . implode(', ', Property::TYPES),
            'price.required' => 'Le prix est obligatoire.',
            'city.required'  => 'La ville est obligatoire.',
        ];
    }
}
