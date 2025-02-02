<?php

namespace App\Http\Requests\Transaction;

use App\Enums\sourceType;
use App\Enums\transactionModeType;
use App\Enums\transactionStatusType;
use App\Enums\transactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'is_convoy' => 'required|boolean',
            'notes.en' => 'nullable|string',
            'notes.ar' => 'nullable|string',
            'sourceable_type' => 'required',new Enum(sourceType::class),
            'sourceable_id' => 'required|integer|exists:users,id|exists:warehouses,id',
            'destinationable_type' => 'required',new Enum(sourceType::class),
            'destinationable_id' => 'required|integer|exists:users,id|exists:warehouses,id',
            'status' => 'required',new Enum(transactionStatusType::class),
            'date' => 'required|date|after:yesterday',
            'transaction_type' => 'required',new Enum(transactionType::class),
            'transaction_mode_type' => new Enum(transactionModeType::class),
            'parent_id' => 'nullable|integer',
            'waybill_num' => 'required|integer',
            'waybill_img' => 'required|image',
            'qr_code' => 'nullable|image',
            'items' => 'required|array',
//            'items.*.warehouse_id' => 'required|exists:warehouses,id',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.CTN' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            // Driver information
            'drivers' => 'required|array',
            'drivers.*.name' => 'required|string|max:255',
            'drivers.*.vehicle_number' => 'required|string|max:255|',
            'drivers.*.national_id' => 'required|string|max:255|',
            'drivers.*.phone' => 'nullable|string|max:20',
            'drivers.*.transportation_comp' => 'nullable|string|max:255',



        ];
    }
}
