<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonorItemResource extends JsonResource
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
            'donor' => $this->user->name,
            'items'=>$this->item->map(function ($Item){
                return new itemsResource($Item);}),
            'branch'=>[
                'id'=>$this->branch->id,
                'name'=>$this->branch->name,
            ],
            'quantity_for_the_donor' => $this->quantity,
        ];
    }
}
