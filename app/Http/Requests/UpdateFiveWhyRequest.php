<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFiveWhyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Không cho phép sửa complaint_id vì nó là khóa ngoại quan trọng
            'what' => ['nullable', 'string', 'max:255'],
            'where' => ['nullable', 'string', 'max:255'],
            'when' => ['nullable', 'string', 'max:255'],
            'who' => ['nullable', 'string', 'max:255'],
            'which' => ['nullable', 'string', 'max:255'],
            'how' => ['nullable', 'string', 'max:255'],
            'phenomenon_description' => ['nullable', 'string'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string', 'url'],
        ];
    }
}