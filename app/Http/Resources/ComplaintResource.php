<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DTOs\ComplaintDTO;

/** @mixin ComplaintDTO */
class ComplaintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // UUID
            'id' => $this->id,
            'general' => [
                'subject' => $this->subject,
                'complaint_no' => $this->complaintNo,
                'type' => $this->type,
                
                // Customer (snake_case keys)
                'customer' => $this->customerName,
                'department' => $this->department,
                'manager' => $this->manager,
                'line_area' => $this->lineArea,
                
                // Details
                'incident_type' => $this->incidentType,
                'product_description' => $this->productDescription,
                'lot_code' => $this->lotCode,
                'product_code' => $this->productCode,
                'machine' => $this->machine,
                'date_code' => $this->dateCode,
                
                // Dates
                'problem_occurrence' => $this->problemOccurrence,
                'problem_detection' => $this->problemDetection,
                'report_time' => $this->reportTime,
                
                // Quantities
                'unit_qty_audited' => $this->unitQtyAudited,
                'unit_qty_rejected' => $this->unitQtyRejected,
                
                // Others
                'severity_level' => $this->severityLevel,
                'category' => $this->category,
                'report_completed_by' => $this->reportCompletedBy,
                'detection_point' => $this->detectionPoint,
                
                // Photos
                'photos' => $this->photos, 
                
                // Partner
                'partner_name' => $this->partnerName,
                'partner_country' => $this->partnerCountry,
                'partner_code' => $this->partnerCode,
                'partner_contact' => $this->partnerContact,
                'partner_photos' => $this->partnerPhotos, 

                'floor_process_visualization' => $this->floorProcessVisualization,
            ]
        ];
    }
}