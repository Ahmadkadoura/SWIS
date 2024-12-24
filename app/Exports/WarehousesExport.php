<?php

namespace App\Exports;

use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WarehousesExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Warehouse::all()->map(function ($warehouse) {
            return [
                'ID' => $warehouse->id,
                'Name' => $warehouse->name,
                'Code' => $warehouse->code,
                'Location' => $warehouse->location ? $warehouse->location->toJson() : null,
                'Branch ID' => $warehouse->branch_id,
                'Capacity' => $warehouse->capacity,
                'Parent ID' => $warehouse->parent_id,
                'User ID' => $warehouse->user_id,
                'Is Distribution Point' => $warehouse->is_Distribution_point ? 'Yes' : 'No',
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Code',
            'Location',
            'Branch ID',
            'Capacity',
            'Parent ID',
            'User ID',
            'Is Distribution Point',
        ];
    }
}
