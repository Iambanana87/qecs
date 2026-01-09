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
            'id' => $this->id,

            // Các trường bạn yêu cầu
            'complaint_no' => $this->complaintNo,
            'customer' => $this->customerName,
            'severity' => $this->severityLevel,
            'incident_type' => $this->incidentType,
            'department' => $this->department,
            'categories' => $this->category,
            'report_completed_by' => $this->reportCompletedBy,
            'occurrence_date' => $this->problemOccurrence,
        ];
    }
}