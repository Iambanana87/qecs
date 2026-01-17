<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEffectivenessCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint_id' => ['required', 'uuid'],
            'produce_cause' => ['nullable', 'boolean'], 
            'no' => ['nullable', 'integer'],
            'action' => ['nullable', 'string'],
            'responsible' => ['nullable', 'string', 'max:255'],
            'end_date' => ['nullable', 'date'],
            'verification' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $toBoolean = function ($key) {
            if ($this->has($key)) {
                $this->merge([$key => filter_var($this->$key, FILTER_VALIDATE_BOOLEAN)]);
            }
        };

        $toBoolean('produce_cause');
        $toBoolean('verification');
    }
}