<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DTOs\ComplaintDTO;

/** @mixin ComplaintDTO */
class ComplaintListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // 1. ID nằm ở ngoài cùng
            'id' => $this->id,

            // 2. Các thông tin khác gom vào trong object 'general'
            'general' => [
                'complaint_no' => $this->complaintNo,
                'customer' => $this->customerName,
                'severity' => $this->severityLevel,
                'incident_type' => $this->incidentType,
                'department' => $this->department,
                'category' => $this->category,
                'report_completed_by' => $this->reportCompletedBy,
                'problem_occurrence' => $this->problemOccurrence,
            ]
        ];
    }
}