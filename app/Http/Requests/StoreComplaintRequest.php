<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'unique:complaints,type'],
            'complaint_no' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string'],
            
            'customer_id' => ['nullable', 'uuid', 'exists:customers,id'],
            'partner_id' => ['nullable', 'uuid', 'exists:partners,id'],
            
            'incident_type' => ['nullable', 'string'],
            'category' => ['nullable', 'string'],
            'severity_level' => ['nullable', 'string'],
            'machine' => ['nullable', 'string'],
            'report_completed_by' => ['nullable', 'string'],
            
            'lot_code' => ['nullable', 'string'],
            'product_code' => ['nullable', 'string'],
            'unit_qty_audited' => ['nullable', 'string'], 
            'unit_qty_rejected' => ['nullable', 'string'],
            'date_code' => ['nullable', 'string'],

            'date_occurrence' => ['nullable', 'date'],
            'date_detection' => ['nullable', 'date'],
            'date_report' => ['nullable', 'date'],
            
            'product_description' => ['nullable', 'string'],
            'detection_point' => ['nullable', 'string'],
            'photo' => ['nullable', 'string'],
            'detection_method' => ['nullable', 'string'],
            'attachment' => ['nullable', 'string'],
            //json array
            'floor_process_visualization' => ['nullable', 'array'], 
        ];
    }
}