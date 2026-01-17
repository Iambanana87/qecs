<?php

namespace App\DTOs;

class EffectivenessCheckDTO
{
    public function __construct(
        public string $id,
        public string $complaintId,
        public bool $produceCause,
        public ?int $no,
        public ?string $action,
        public ?string $responsible,
        public ?string $endDate,
        public bool $verification,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromDb(object $row): self
    {
        return new self(
            id: $row->id,
            complaintId: $row->complaint_id,
            produceCause: (bool) $row->produce_cause, 
            no: $row->no,
            action: $row->action,
            responsible: $row->responsible,
            endDate: $row->end_date,
            verification: (bool) $row->verification, 
            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
        );
    }
}