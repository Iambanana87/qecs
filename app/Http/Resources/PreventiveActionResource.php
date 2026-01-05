<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DTOs\PreventiveActionDTO;

/** @mixin PreventiveActionDTO */
class PreventiveActionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'complaint_id' => $this->complaintId,
            'no' => $this->no,
            'action' => $this->action,
            'responsible' => $this->responsible,
            'end_date' => $this->endDate,
            'verification' => $this->verification,
            
            'complaint_responsible' => $this->complaintResponsible,
            'production_representative' => $this->productionRepresentative,
            'quality_representative' => $this->qualityRepresentative,
            'engineering_representative' => $this->engineeringRepresentative,
            'quality_manager' => $this->qualityManager,
            
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}