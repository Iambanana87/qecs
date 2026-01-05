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
        return [
            'id' => $this->id,
            'complaint_id' => $this->complaintId,
            
            'what' => $this->what,
            'where' => $this->where,
            'when' => $this->when,
            'who' => $this->who,
            'which' => $this->which,
            'how' => $this->how,
            
            'phenomenon_description' => $this->phenomenonDescription,
            'photos' => $this->photos, 

            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}