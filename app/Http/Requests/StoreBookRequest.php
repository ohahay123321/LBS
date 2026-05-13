<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'isbn' => 'required|string|unique:books,isbn',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'stock' => 'required|integer|min:1',
            'category' => 'required|string|exists:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.unique' => 'A book with this ISBN already exists.',
            'image.image' => 'The file must be an image.',
            'image.max' => 'The image must not exceed 2MB.',
        ];
    }
}
