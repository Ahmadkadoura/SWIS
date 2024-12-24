<?php

namespace App\Http\Resources;

use App\Enums\sectorType;
use App\Enums\unitType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class indexItemForDonerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'donor' => $this->user->name,
            'item' => [
                'id' => $this->item->id,
                'name' => $this->item->name,
                'code' => $this->item->code,
                'sectorType' => __($this->item->sectorType->name),
                'unitType' => __($this->item->unitType->name) ,
                'size' => $this->item->size,
                'weight' => $this->item->weight,
            ],
            'branch'=>[
                'id'=>$this->branch->id,
                'name'=>$this->branch->name,
            ],
            'quantity' => $this->quantity,
        ];
    }
}
