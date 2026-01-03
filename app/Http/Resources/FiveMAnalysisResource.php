<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DTOs\FiveMAnalysisDTO;

/** @mixin FiveMAnalysisDTO */
class FiveMAnalysisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'complaint_id' => $this->complaintId,
            'type' => $this->type,

            'code' => $this->code,
            'cause' => $this->cause,
            'confirmed' => $this->confirmed,
            'description' => $this->description,

            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
