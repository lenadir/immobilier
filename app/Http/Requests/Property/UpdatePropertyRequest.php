<?php

namespace App\Http\Requests\Property;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isAdmin() || $user->isAgent());
    }

    public function rules(): array
    {
        return [
            'type'         => ['sometimes', Rule::in(Property::TYPES)],
            'price'        => ['sometimes', 'numeric', 'min:0'],
            'city'         => ['sometimes', 'string', 'max:100'],
            'status'       => ['sometimes', Rule::in(Property::STATUSES)],
            'rooms'        => ['sometimes', 'nullable', 'integer', 'min:1', 'max:100'],
            'surface'      => ['sometimes', 'nullable', 'numeric', 'min:1'],
            'address'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'description'  => ['sometimes', 'nullable', 'string', 'max:5000'],
            'is_published' => ['sometimes', 'boolean'],
        ];
    }
}
