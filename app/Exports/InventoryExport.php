<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryExport implements FromCollection, WithHeadings
{
    protected $inventoryData;

    public function __construct(Collection $inventoryData)
    {
        $this->inventoryData = $inventoryData;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->inventoryData;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Item ID',
            'Item Name',
            'Total Quantity In',
            'Total Quantity Out',
            'Quantity In Warehouse',
        ];
    }
}
