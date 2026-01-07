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
            'id' => $this->id,
            'type' => $this->type,
            'complaint_no' => $this->complaintNo,
            'subject' => $this->subject,
            
            // Trả về Object con cho Customer và Partner để Frontend dễ dùng
            'customer' => $this->customerId ? [
                'id' => $this->customerId,
                'name' => $this->customerName,
            ] : null,

            'partner' => $this->partnerId ? [
                'id' => $this->partnerId,
                'name' => $this->partnerName,
            ] : null,

            'five_why_id' => $this->fiveWhyId,

            'incident_info' => [
                'type' => $this->incidentType,
                'category' => $this->category,
                'severity_level' => $this->severityLevel,
                'machine' => $this->machine,
                'report_completed_by' => $this->reportCompletedBy,
            ],
            
            'product_info' => [
                'lot_code' => $this->lotCode,
                'product_code' => $this->productCode,
                'unit_qty_audited' => $this->unitQtyAudited,
                'unit_qty_rejected' => $this->unitQtyRejected,
                'date_code' => $this->dateCode,
            ],
            
            'dates' => [
                'occurrence' => $this->dateOccurrence,
                'detection' => $this->dateDetection,
                'report' => $this->dateReport,
            ],
            
            'description' => $this->productDescription,
            'detection_point' => $this->detectionPoint,
            'photo' => $this->photo,
            'detection_method' => $this->detectionMethod,
            'attachment' => $this->attachment,
            'floor_process_visualization' => $this->floorProcessVisualization,

            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}