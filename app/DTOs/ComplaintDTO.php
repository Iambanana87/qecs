<?php

namespace App\DTOs;

class ComplaintDTO
{
    public function __construct(
        public string $id,
        public string $type,
        public ?string $complaintNo,
        public ?string $subject,
        
        // Thông tin liên kết (Join Data)
        public ?string $customerId,
        public ?string $customerName,      
        public ?string $partnerId,
        public ?string $partnerName,      
        public ?string $fiveWhyId,

        // Chi tiết sự cố
        public ?string $incidentType,
        public ?string $category,
        public ?string $severityLevel,
        public ?string $machine,
        public ?string $reportCompletedBy,
        
        // Thông tin sản phẩm
        public ?string $lotCode,
        public ?string $productCode,
        public ?string $unitQtyAudited,
        public ?string $unitQtyRejected,
        public ?string $dateCode,
        
        // Ngày tháng
        public ?string $dateOccurrence,
        public ?string $dateDetection,
        public ?string $dateReport,
        
        // Mô tả & Ảnh
        public ?string $productDescription,
        public ?string $detectionPoint,
        public ?string $photo,
        public ?string $detectionMethod,
        public ?string $attachment,
        public array $floorProcessVisualization, 

        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromDb(object $row): self
    {
        // Helper decode JSON
        $decodeJson = function ($json) {
            if (empty($json)) return [];
            $decoded = json_decode($json, true);
            return is_array($decoded) ? $decoded : [];
        };

        return new self(
            id: $row->id,
            type: $row->type,
            complaintNo: $row->complaint_no,
            subject: $row->subject,
            
            customerId: $row->customer_id,
            customerName: $row->customer_name ?? null, 
            partnerId: $row->partner_id,
            partnerName: $row->partner_name ?? null,   
            fiveWhyId: $row->five_why_id,

            incidentType: $row->incident_type,
            category: $row->category,
            severityLevel: $row->severity_level,
            machine: $row->machine,
            reportCompletedBy: $row->report_completed_by,
            
            lotCode: $row->lot_code,
            productCode: $row->product_code,
            unitQtyAudited: $row->unit_qty_audited,
            unitQtyRejected: $row->unit_qty_rejected,
            dateCode: $row->date_code,
            
            dateOccurrence: $row->date_occurrence,
            dateDetection: $row->date_detection,
            dateReport: $row->date_report,
            
            productDescription: $row->product_description,
            detectionPoint: $row->detection_point,
            photo: $row->photo,
            detectionMethod: $row->detection_method,
            attachment: $row->attachment,
            floorProcessVisualization: $decodeJson($row->floor_process_visualization),

            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
        );
    }
}