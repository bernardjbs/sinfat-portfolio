<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string'],
            'excerpt'      => ['nullable', 'string', 'max:500'],
            'category'     => ['nullable', 'string', 'max:100'],
            'status'       => ['required', 'in:draft,published'],
            'ai_generated' => ['boolean'],
            'ai_model'     => ['nullable', 'string', 'max:100'],
        ];
    }
}
