<?php

namespace App\DTOs;

class ImmediateActionDTO
{
    public function __construct(
        public string $id,
        public string $complaintId,
        public ?int $no,
        public ?string $action,
        public ?string $status,
        public ?string $responsible,
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
            status: $row->status,
            responsible: $row->responsible,
            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
        );
    }
}