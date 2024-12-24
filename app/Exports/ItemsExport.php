<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsExport implements FromCollection, WithHeadings
{
 public function __construct($items)
{
    $this->items = $items;
}
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->items->map(function ($item) {
            return [
                'ID' => $item->id,
                'Name (EN)' => $item->getTranslation('name', 'en'),
                'Name (AR)' => $item->getTranslation('name', 'ar'),
                'Code' => $item->code,
                'Sector' => $item->sectorType->name,
                'Unit' => $item->unitType->name,
                'Size' => $item->size,
                'Weight' => $item->weight,
                'Quantity' => $item->quantity,
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
            'Name (EN)',
            'Name (AR)',
            'Code',
            'Sector',
            'Unit',
            'Size',
            'Weight',
            'Quantity',
        ];
    }



}
