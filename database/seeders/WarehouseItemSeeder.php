<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Warehouse;
use App\Models\WarehouseItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lasti=Warehouse::pluck('id');
        $lastj=Item::pluck('id');
        for ($i = 1; $i <= $lasti->count(); $i++) {
            for ($j = 1; $j <= $lastj->count(); $j++) {
                WarehouseItem::create([
                    'warehouse_id' => $i,
                    'item_id' => $j,
                    'quantity' => rand(1,500),
                ]);
            }
        }
    }
}
