<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ctn extends Model
{
    use HasFactory;

    // Table name (optional if Laravel's pluralization would be incorrect)
    protected $table = 'ctns';

    // Mass assignable attributes
    protected $fillable = [
        'warehouse_item_id',
        'item_id',
        'quantity',
        'CTN'
    ];

    // Relationships
    public function warehouseItem()
    {
        return $this->belongsTo(WarehouseItem::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
