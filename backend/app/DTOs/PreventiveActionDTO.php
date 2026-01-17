<?php

namespace App\DTOs;

class PreventiveActionDTO
{
    public function __construct(
        public string $id,
        public string $complaintId,
        public ?int $no,
        public ?string $action,
        public ?string $responsible,
        public ?string $endDate,
        public bool $verification,
        
        public ?string $complaintResponsible,
        public ?string $productionRepresentative,
        public ?string $qualityRepresentative,
        public ?string $engineeringRepresentative,
        public ?string $qualityManager,

        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromDb(object $row): self
    {
        return new self(
            id: $row->id,
            complaintId: $row->complaint_id,
            no: $row->no,
            action: $row->action,
            responsible: $row->responsible,
            endDate: $row->end_date,
            verification: (bool) $row->verification,
            
            complaintResponsible: $row->complaint_responsible,
            productionRepresentative: $row->production_representative,
            qualityRepresentative: $row->quality_representative,
            engineeringRepresentative: $row->engineering_representative,
            qualityManager: $row->quality_manager,
            
            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
        );
    }
}