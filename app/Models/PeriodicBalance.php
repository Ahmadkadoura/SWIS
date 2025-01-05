<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodicBalance extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'balance_date',
        'balance',
    ];

    /**
     * Relationship with the Item model.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relationship with the Warehouse model.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
