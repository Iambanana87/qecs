<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DTOs\EffectivenessCheckDTO;

/** @mixin EffectivenessCheckDTO */
class EffectivenessCheckResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'complaint_id' => $this->complaintId,
            'produce_cause' => $this->produceCause,
            'no' => $this->no,
            'action' => $this->action,
            'responsible' => $this->responsible,
            'end_date' => $this->endDate,
            'verification' => $this->verification,
            
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}