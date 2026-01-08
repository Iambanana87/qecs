<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // General
            'type' => ['required', 'string', 'unique:complaints,type'],
            'complaint_no' => ['nullable', 'string'],
            'subject' => ['nullable', 'string'],
            'customer_id' => ['nullable', 'uuid', 'exists:customers,id'],
            'partner_id' => ['nullable', 'uuid', 'exists:partners,id'],
            
            // Quantities (snake_case)
            'unit_qty_audited' => ['nullable', 'numeric'],
            'unit_qty_rejected' => ['nullable', 'numeric'],
            
            // Dates (snake_case)
            'problem_occurrence' => ['nullable', 'date'],
            'problem_detection' => ['nullable', 'date'],
            'report_time' => ['nullable', 'date'],

            // Photos (snake_case)
            'photos' => ['nullable', 'array'],
            'photos.*' => ['string', 'url'],

            // Partner Photos (snake_case)
            'partner_photos' => ['nullable', 'array'],
            'partner_photos.*' => ['string', 'url'],

            // Details (snake_case matching DB columns mostly)
            'incident_type' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'severity_level' => ['nullable', 'string'],
            'product_description' => ['nullable', 'string'],
            'lot_code' => ['nullable', 'string'],
            'product_code' => ['nullable', 'string'],
            'machine' => ['nullable', 'string'],
            'report_completed_by' => ['nullable', 'string'],
            'detection_point' => ['nullable', 'string'],
            'date_code' => ['nullable', 'string'],
        ];
    }
}