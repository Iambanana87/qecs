<?php

namespace App\DTOs;

class CheckParametersOperationDTO
{
    public function __construct(
        public string $id,
        public string $complaintId,
        public ?int $no,
        public ?string $machine,
        public ?string $subAssembly,
        public ?string $component,
        public ?string $description,
        public ?string $currentCondition,
        public array $beforePhoto, // Mảng ảnh
        public array $afterPhoto,  // Mảng ảnh
        public ?string $respons,
        public ?string $controlFrequency,
        public ?string $status,
        public ?string $closeDate,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function fromDb(object $row): self
    {
        // Helper decode JSON an toàn
        $decodePhoto = function ($json) {
            if (empty($json)) return [];
            $decoded = json_decode($json, true);
            return is_array($decoded) ? $decoded : [];
        };

        return new self(
            id: $row->id,
            complaintId: $row->complaint_id,
            no: $row->no,
            machine: $row->machine,
            subAssembly: $row->sub_assembly,
            component: $row->component,
            description: $row->description,
            currentCondition: $row->current_condition,
            
            beforePhoto: $decodePhoto($row->before_photo),
            afterPhoto: $decodePhoto($row->after_photo),
            
            respons: $row->respons,
            controlFrequency: $row->control_frequency,
            status: $row->status,
            closeDate: $row->close_date,
            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
        );
    }
}