<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\FiveMType;

class StoreFiveMAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint_id' => ['required', 'uuid'],

            'type' => [
                'required',
                'string',
                Rule::in(FiveMType::values()),
            ],

            'code' => ['nullable', 'string', 'max:255'],
            'cause' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'confirmed' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // đảm bảo confirmed luôn là boolean
        if ($this->has('confirmed')) {
            $this->merge([
                'confirmed' => filter_var($this->confirmed, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}
