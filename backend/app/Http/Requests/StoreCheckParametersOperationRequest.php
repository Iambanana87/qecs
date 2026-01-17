<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckParametersOperationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint_id' => ['required', 'uuid'],
            'no' => ['nullable', 'integer'],
            'machine' => ['nullable', 'string', 'max:255'],
            'sub_assembly' => ['nullable', 'string', 'max:255'],
            'component' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'current_condition' => ['nullable', 'string'],
            
            'before_photo' => ['nullable', 'array'],
            'before_photo.*' => ['string', 'url'], // Mỗi ảnh là 1 URL
            'after_photo' => ['nullable', 'array'],
            'after_photo.*' => ['string', 'url'],

            'respons' => ['nullable', 'string', 'max:255'],
            'control_frequency' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'close_date' => ['nullable', 'date'],
        ];
    }
}