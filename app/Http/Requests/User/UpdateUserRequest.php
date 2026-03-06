<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled in UserService::update()
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name'      => ['sometimes', 'string', 'max:100'],
            'phone'     => ['sometimes', 'nullable', 'string', 'max:20'],
            'role'      => ['sometimes', Rule::in([User::ROLE_ADMIN, User::ROLE_AGENT, User::ROLE_GUEST])],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
