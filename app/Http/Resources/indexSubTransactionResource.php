<?php

namespace App\Http\Resources;

use App\Enums\sourceType;
use App\Enums\transactionModeType;
use App\Enums\transactionType;
use App\Enums\transactionStatusType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class indexSubTransactionResource extends JsonResource
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
            'is_convoy' => $this->is_convoy,
            'notes' => $this->notes,
            'code' => $this->code,
            'status' => __( $this->status->name) ,
            'date' => $this->date,
            'transaction_type' =>  __($this->transaction_type->name) ,
            'transaction_mode_type' =>  __($this->transaction_mode_type->name ),
            'waybill_num' => $this->waybill_num,
            'waybill_img' => $this->imageUrl('waybill_img'),
            'qr_code' => $this->imageUrl('qr_code'),

            // Handling the sourceable relation
            'source' => [
                'type' =>  __($this->sourceable_type->name),  // Use ->value to get the enum value
                'id' => $this->sourceable->id ?? null,
                'name' => $this->sourceable->name ?? null,  // Assuming the sourceable has a 'name' attribute
            ],

            // Handling the destinationable relation
            'destination' => [
                'type' =>__($this->destinationable_type->name ),  // Use ->value to get the enum value
                'id' => $this->destinationable->id ?? null,
                'name' => $this->destinationable->name ?? null, // Assuming the destinationable has a 'name' attribute
            ],

            // Handling the transaction items
            'details' => $this->transactionitem->map(function ($transactionItem) {
                return [
                    'item' => $transactionItem->item->name ?? null,
                    'CTN' => $transactionItem->CTN,
                    'quantity' => $transactionItem->quantity,
                ];
            }),
        ];
    }
}
