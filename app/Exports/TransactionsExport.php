<?php

namespace App\Exports;

use App\Models\Transaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromCollection, WithHeadings
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->transactions->map(function ($transaction) {
            return [
                'User ID' => $transaction->user_id,
                'Is Convoy' => $transaction->is_convoy ? 'Yes' : 'No',
                'Notes' => $transaction->notes,
                'Code' => $transaction->code,
                'Status' => $transaction->status->name, // Assuming you want to export the status type directly
                'Date' => Carbon::parse($transaction->date)->format('Y-m-d'), // Parse the date string and format it
                'Waybill Number' => $transaction->waybill_num,
                'Waybill Image' => $transaction->waybill_img,
                'QR Code' => $transaction->qr_code,
                'CTN' => $transaction->CTN,
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'User ID',
            'Is Convoy',
            'Notes',
            'Code',
            'Status',
            'Date',
            'Waybill Number',
            'Waybill Image',
            'QR Code',
            'CTN',
        ];
    }
}
