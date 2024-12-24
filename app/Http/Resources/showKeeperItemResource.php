<?php

namespace App\Http\Resources;

use App\Enums\sectorType;
use App\Enums\unitType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class showKeeperItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $item=$this->item;
        return [
            'id' => $item->id,
            'name' => $item->name,
            'code' => $item->code,
            'sectorType' =>  __($this->sectorType->name) ,
            'unitType' =>  __($this->unitType->name ),
            'size' => $item->size,
            'weight' => $item->weight,
            'quantity' => $this->quantity,
        ];
    }
}
