<?php

namespace App\Http\Resources;

use App\Enums\sourceType;
use App\Enums\transactionModeType;
use App\Enums\transactionStatusType;
use App\Enums\transactionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class indexMainTransactionResource extends JsonResource
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
            'status' => __($this->status->name) ,
            'date' => $this->date,
            'transaction_type' => __($this->transaction_type->name) ,
            'transaction_mode_type' =>__($this->transaction_mode_type->name) ,
            'waybill_num' => $this->waybill_num,
            'waybill_img' => $this->imageUrl('waybill_img'),
            'qr_code' => $this->imageUrl('qr_code'),

            // Handling the sourceable relation
            'source' => [
                'type' =>  __($this->sourceable_type->name),  // Use ->value to get the enum value
                'id' => $this->sourceable->id,
                'name' => $this->sourceable->name,  // Assuming the sourceable has a 'name' attribute
            ],

            //Handling the destinationables relation
            'destinations' => $this->childTransactions->map(function ($childTran) {
//                dd($childTran);
                return [
                    'type' => __($childTran->destinationable_type->name) ,  // Use ->value to get the enum value
                    'id' => $childTran->destinationable->id , // Ensure that this is 'distianationable' or correct it to 'destinationable'
                    'name' =>  $childTran->destinationable->name , // Access the name through the relation
                ];
            }),


//             Handling the transaction warehouse items
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
