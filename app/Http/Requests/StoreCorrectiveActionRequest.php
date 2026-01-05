<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCorrectiveActionRequest extends FormRequest
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
            'action' => ['nullable', 'string'],
            'responsible' => ['nullable', 'string', 'max:255'],
            'end_date' => ['nullable', 'date'],
            'verification' => ['nullable', 'boolean'], 
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