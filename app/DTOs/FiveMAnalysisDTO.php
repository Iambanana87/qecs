<?php

namespace App\DTOs;

class FiveMAnalysisDTO
{
    public function __construct(
        public string $id,
        public string $complaintId,
        public string $type,
        public ?string $code,
        public ?string $cause,
        public bool $confirmed,
        public ?string $description,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromDb(object $row): self
    {
        return new self(
            id: $row->id,
            complaintId: $row->complaint_id,
            type: $row->type,
            code: $row->code,
            cause: $row->cause,
            confirmed: (bool) $row->confirmed,
            description: $row->description,
            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
        );
    }
}
