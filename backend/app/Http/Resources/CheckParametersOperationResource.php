<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\DTOs\CheckParametersOperationDTO;

/** @mixin CheckParametersOperationDTO */
class CheckParametersOperationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'complaint_id' => $this->complaintId,
            'no' => $this->no,
            'machine' => $this->machine,
            'sub_assembly' => $this->subAssembly,
            'component' => $this->component,
            'description' => $this->description,
            'current_condition' => $this->currentCondition,
            
            'before_photo' => $this->beforePhoto,
            'after_photo' => $this->afterPhoto,
            
            'respons' => $this->respons,
            'control_frequency' => $this->controlFrequency,
            'status' => $this->status,
            'close_date' => $this->closeDate,
            
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}