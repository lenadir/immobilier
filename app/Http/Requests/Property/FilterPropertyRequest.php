<?php

namespace App\Http\Requests\Property;

use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterPropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city'      => ['sometimes', 'string', 'max:100'],
            'type'      => ['sometimes', Rule::in(Property::TYPES)],
            'status'    => ['sometimes', Rule::in(Property::STATUSES)],
            'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'numeric', 'min:0', 'gte:min_price'],
            'search'    => ['sometimes', 'string', 'max:255'],
            'per_page'  => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by'   => ['sometimes', Rule::in(['price', 'created_at', 'surface', 'rooms'])],
            'sort_dir'  => ['sometimes', Rule::in(['asc', 'desc'])],
        ];
    }
}
