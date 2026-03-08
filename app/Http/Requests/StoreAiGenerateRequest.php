<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAiGenerateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topic'   => ['required', 'string', 'max:500'],
            'context' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
