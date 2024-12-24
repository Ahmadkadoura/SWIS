<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class WarehouseItem extends Model implements Searchable
{
    use HasFactory,SoftDeletes;
    protected $fillable=[
        'warehouse_id',
        'item_id',
        'quantity',
    ];
    public function getSearchResult(): SearchResult
    {
        $url = route('warehouses_items.search', $this->slug);
        return new SearchResult($this, $this->name, $url);
    }
    public function item():BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse():BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function ctns():HasMany
    {
        return $this->hasMany(Ctn::class);
    }
}
