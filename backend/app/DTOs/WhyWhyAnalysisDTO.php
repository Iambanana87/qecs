<?php

namespace App\DTOs;

class WhyWhyAnalysisDTO
{
    public function __construct(
        public string $id,
        public string $complaintId,
        public string $analysisType,
        public ?string $why1,
        public ?string $why2,
        public ?string $why3,
        public ?string $why4,
        public ?string $why5,
        public ?string $rootCause,
        public ?string $capaRef,
        public ?string $status,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromDb(object $row): self
    {
        return new self(
            id: $row->id,
            complaintId: $row->complaint_id,
            analysisType: $row->analysis_type,
            why1: $row->why1,
            why2: $row->why2,
            why3: $row->why3,
            why4: $row->why4,
            why5: $row->why5,
            rootCause: $row->root_cause,
            capaRef: $row->capa_ref,
            status: $row->status,
            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
        );
    }
}