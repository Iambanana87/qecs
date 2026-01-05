<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePreventiveActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no' => ['nullable', 'integer'],
            'action' => ['nullable', 'string'],
            'responsible' => ['nullable', 'string', 'max:255'],
            'end_date' => ['nullable', 'date'],
            'verification' => ['nullable', 'boolean'],
            
            'complaint_responsible' => ['nullable', 'string', 'max:255'],
            'production_representative' => ['nullable', 'string', 'max:255'],
            'quality_representative' => ['nullable', 'string', 'max:255'],
            'engineering_representative' => ['nullable', 'string', 'max:255'],
            'quality_manager' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('verification')) {
            $this->merge([
                'verification' => filter_var($this->verification, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}