<?php

namespace App\Http\Requests\Image;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isAdmin() || $user->isAgent());
    }

    public function rules(): array
    {
        return [
            // Accepte soit un seul fichier, soit un tableau de fichiers
            'images'          => ['required', 'array', 'min:1', 'max:10'],
            'images.*'        => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,webp',
                'max:5120',   // 5 Mo par image
            ],
            'is_cover'        => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required'    => 'Au moins une image est requise.',
            'images.*.mimes'     => 'Les formats acceptés sont : JPEG, PNG, WebP.',
            'images.*.max'       => "Chaque image ne doit pas dépasser 5 Mo.",
        ];
    }
}
