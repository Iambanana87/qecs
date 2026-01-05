<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\WhyWhyType;

class StoreWhyWhyAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'complaint_id' => ['required', 'uuid'],
            
            'analysis_type' => [
                'required', 
                'string', 
                Rule::in(WhyWhyType::values())
            ],

            'why1' => ['nullable', 'string'],
            'why2' => ['nullable', 'string'],
            'why3' => ['nullable', 'string'],
            'why4' => ['nullable', 'string'],
            'why5' => ['nullable', 'string'],
            
            'root_cause' => ['nullable', 'string'],
            'capa_ref' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
        ];
    }
}