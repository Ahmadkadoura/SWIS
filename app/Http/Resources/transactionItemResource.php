<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class transactionItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        var_dump($this->id);

        return [
            'id' => $this->id,
            'transaction_id'=> $this->transaction_id,
            'item' => $this->item->name ?? null ,
            'CTN' => $this->CTN,
            'quantity' => $this->quantity,
        ];
    }
}
