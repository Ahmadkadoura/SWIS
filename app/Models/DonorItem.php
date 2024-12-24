<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonorItem extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'branch_id',
        'quantity',
    ];

    /**
     * Get the donor that owns the donorItem.
     */
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the item that is associated with the donorItem.
     */
    public function item():BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
    public function branch():BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
