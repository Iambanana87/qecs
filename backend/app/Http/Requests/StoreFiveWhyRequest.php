<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFiveWhyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint_id' => ['required', 'uuid'],
            
            'what' => ['nullable', 'string', 'max:255'],
            'where' => ['nullable', 'string', 'max:255'],
            'when' => ['nullable', 'string', 'max:255'],
            'who' => ['nullable', 'string', 'max:255'],
            'which' => ['nullable', 'string', 'max:255'],
            'how' => ['nullable', 'string', 'max:255'],
            
            'phenomenon_description' => ['nullable', 'string'],
            
            // Validate mảng link ảnh
            'photos' => ['nullable', 'array'], 
            'photos.*' => ['string', 'url'], // Mỗi phần tử phải là string (hoặc url nếu muốn chặt hơn)
        ];
    }
}