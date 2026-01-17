<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

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
        
        public string $createdAt,
        public string $updatedAt,
        
        // Truyền vào danh sách toàn bộ file của record này
        public Collection $attachments 
    ) {}

    // Hàm fromDb nhận thêm tham số $attachments
    public static function fromDb(object $row, Collection $attachments): self
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
            
            createdAt: (string) $row->created_at,
            updatedAt: (string) $row->updated_at,
            
            attachments: $attachments // Truyền nguyên collection vào
        );
    }
}