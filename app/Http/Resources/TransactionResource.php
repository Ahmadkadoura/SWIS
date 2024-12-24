<?php

namespace App\Http\Resources;

use App\Enums\transactionModeType;
use App\Enums\transactionStatusType;
use App\Enums\transactionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray(Request $request): array
    {
        // Check if the parent_id is null
        if ($this->resource->parent_id === null) {
            // If parent_id is null, use IndexMainTransactionResource
            return (new IndexMainTransactionResource($this->resource))->toArray($request);
        } else {
            // If parent_id is not null, use IndexSubTransactionResource
            return (new IndexSubTransactionResource($this->resource))->toArray($request);
        }

    }
}
