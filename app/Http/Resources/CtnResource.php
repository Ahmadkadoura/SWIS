<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CtnResource extends JsonResource
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
        'warehouse_item_id' => $this->warehouseItem->warehouse->name,
        'item_id' => $this->item_id,
        'quantity' => $this->quantity,
        'CTN' => $this->CTN,
    ];
    }
}
