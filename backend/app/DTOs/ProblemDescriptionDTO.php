<?php

namespace App\DTOs;

class ProblemDescriptionDTO
{
    public function __construct(
        public string $id,
        public string $complaintId,
        public ?string $what,
        public ?string $where,
        public ?string $when,
        public ?string $who,
        public ?string $which,
        public ?string $how,
        public ?string $phenomenonDescription,
        public ?array $photos, // Chuyển từ JSON text sang Array
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromDb(object $row): self
    {
        return new self(
            id: $row->id,
            complaintId: $row->complaint_id,
            what: $row->what,
            where: $row->where,
            when: $row->when,
            who: $row->who,
            which: $row->which,
            how: $row->how,
            phenomenonDescription: $row->phenomenon_description,
            
            photos: $row->photos ? json_decode($row->photos, true) : [],
            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
        );
    }
}