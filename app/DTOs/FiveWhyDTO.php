<?php

namespace App\DTOs;

class FiveWhyDTO
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
        public array $photos, // Luôn trả về mảng (dù rỗng)
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromDb(object $row): self
    {
        // Xử lý photos: DB lưu text/json -> PHP convert về mảng
        $photos = [];
        if (!empty($row->photos)) {
            $decoded = json_decode($row->photos, true);
            $photos = is_array($decoded) ? $decoded : [];
        }

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
            photos: $photos,
            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
        );
    }
}