<?php

namespace App\Http\Resources;

use App\Enums\sectorType;
use App\Enums\unitType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class itemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'sectorType' =>  __($this->sectorType->name) ,
            'unitType' =>  __($this->unitType->name ),
            'size' => $this->size,
            'weight' => $this->weight,
            'quantity_in_the_system' => $this->quantity,
            'status'=>__($this->statusType->name),
        ];
    }
}
