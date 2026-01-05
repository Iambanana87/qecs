<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DTOs\WhyWhyAnalysisDTO;

/** @mixin WhyWhyAnalysisDTO */
class WhyWhyAnalysisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'complaint_id' => $this->complaintId,
            'analysis_type' => $this->analysisType,
            
            'why1' => $this->why1,
            'why2' => $this->why2,
            'why3' => $this->why3,
            'why4' => $this->why4,
            'why5' => $this->why5,
            
            'root_cause' => $this->rootCause,
            'capa_ref' => $this->capaRef,
            'status' => $this->status,
            
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
