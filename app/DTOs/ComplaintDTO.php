<?php

namespace App\DTOs;

class ComplaintDTO
{
    public function __construct(
        // General Info
        public ?string $subject,
        public ?string $complaintNo,
        public string $type,
        
        // Customer Info
        public ?string $customerName,
        public ?string $department,
        public ?string $manager,
        public ?string $lineArea,

        // Incident Details
        public ?string $incidentType,
        public ?string $productDescription,
        public ?string $lotCode,
        public ?string $productCode,
        public ?string $machine,
        public ?string $dateCode,
        
        // Dates
        public ?string $problemOccurrence,
        public ?string $problemDetection,
        public ?string $reportTime,
        
        // Quantities
        public int $unitQtyAudited,
        public int $unitQtyRejected,
        
        // Severity
        public ?string $severityLevel,
        public ?string $category,
        public ?string $reportCompletedBy,
        public ?string $detectionPoint,
        
        // Photos
        public array $photos,
        
        // Partner Info
        public ?string $partnerName,
        public ?string $partnerCountry,
        public ?string $partnerCode,
        public ?string $partnerContact,
        public array $partnerPhotos,
    ) {}

    public static function fromDb(object $row): self
    {
        $decodeArray = function ($json) {
            if (empty($json)) return [];
            $decoded = json_decode($json, true);
            return is_array($decoded) ? $decoded : [];
        };

        return new self(
            subject: $row->subject,
            complaintNo: $row->complaint_no,
            type: $row->type,
            
            customerName: $row->cust_name ?? null,
            department: $row->cust_department ?? null,
            manager: $row->cust_manager ?? null,
            lineArea: $row->cust_line_area ?? null,

            incidentType: $row->incident_type,
            productDescription: $row->product_description,
            lotCode: $row->lot_code,
            productCode: $row->product_code,
            machine: $row->machine,
            dateCode: $row->date_code,
            
            problemOccurrence: $row->date_occurrence,
            problemDetection: $row->date_detection,
            reportTime: $row->date_report,
            
            unitQtyAudited: (int) ($row->unit_qty_audited ?? 0),
            unitQtyRejected: (int) ($row->unit_qty_rejected ?? 0),
            
            severityLevel: $row->severity_level,
            category: $row->category,
            reportCompletedBy: $row->report_completed_by,
            detectionPoint: $row->detection_point,
            
            photos: $decodeArray($row->photo),
            
            partnerName: $row->part_name ?? null,
            partnerCountry: $row->part_country ?? null,
            partnerCode: $row->part_code ?? null,
            partnerContact: $row->part_contact ?? null,
            partnerPhotos: $decodeArray($row->attachment),
        );
    }
}