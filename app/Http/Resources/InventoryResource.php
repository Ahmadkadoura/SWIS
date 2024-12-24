<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'item_id' => $this['item_id'],
            'item_name' => $this['item_name'],
            'total_quantity_in' => $this->when($this['total_quantity_in'] > 0, $this['total_quantity_in']),
            'total_quantity_out' => $this->when($this['total_quantity_out'] > 0, $this['total_quantity_out']),
            'quantity_in_warehouse' => $this['quantity_in_warehouse'],

        ];

    }
}
