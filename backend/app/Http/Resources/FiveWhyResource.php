<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DTOs\FiveWhyDTO;

/** @mixin FiveWhyDTO */
class FiveWhyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // $this->attachments là Collection lấy từ DTO
        
        $getFiles = function ($sectionName) {
            return $this->attachments
                ->where('section', $sectionName)
                ->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'name' => $file->file_name,
                        // Tạo URL đầy đủ để frontend hiển thị
                        'url' => asset('uploads/' . $file->file_path), 
                        'type' => $file->file_type
                    ];
                })
                ->values(); // Reset key mảng
        };

        return [
            'id' => $this->id,
            'problemDescription' => [
                'what' => [
                    'description' => $this->what,
                    'attachment'  => $getFiles('what'),
                ],
                'where' => [
                    'description' => $this->where,
                    'attachment'  => $getFiles('where'),
                ],
                'when' => [
                    'description' => $this->when,
                    'attachment'  => $getFiles('when'),
                ],
                'who' => [
                    'description' => $this->who,
                    'attachment'  => $getFiles('who'),
                ],
                'which' => [
                    'description' => $this->which,
                    'attachment'  => $getFiles('which'),
                ],
                'how' => [
                    'description' => $this->how,
                    'attachment'  => $getFiles('how'),
                ],
                'phenomenonDescription' => $this->phenomenonDescription,
            ],
            'created_at' => $this->createdAt,
        ];
    }
}