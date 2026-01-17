<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DTOs\ImmediateActionDTO;

/** @mixin ImmediateActionDTO */
class ImmediateActionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'complaint_id' => $this->complaintId,
            'no' => $this->no,
            'action' => $this->action,
            'status' => $this->status,
            'responsible' => $this->responsible,
            
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}