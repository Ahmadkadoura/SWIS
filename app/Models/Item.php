<?php

namespace App\Models;

use App\Enums\itemStatusType;
use App\Enums\sectorType;
use App\Enums\unitType;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\Translatable\HasTranslations;

class Item extends Model implements Searchable
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['name'];
    protected $fillable = [
        'name',
        'code',
        'sectorType',
        'unitType',
        'size',
        'weight',
        'quantity',
        'statusType',
    ];
    protected $casts=[
        'unitType'=>unitType::class ,
        'sectorType'=>sectorType::class ,
        'statusType'=>itemStatusType::class ,
    ];
    public function getSearchResult(): SearchResult
    {
        $url = route('items.search', $this->slug);
        return new SearchResult($this, $this->name, $url);
    }
    public function scopeSize(Builder $query, $less=0 , $great=1e10): Builder
    {
        return $query->whereBetween('size',[$less , $great]);
    }
    public function scopeWeight(Builder $query, $less=0 , $great=1e10): Builder
    {
        return $query->whereBetween('weight',[$less , $great]);
    }
    public function scopeQuantity(Builder $query, $less=0 , $great=1e10): Builder
    {
        return $query->whereBetween('quantity',[$less , $great]);
    }
    public function warehouseItem()
    {
        return $this->hasMany(WarehouseItem::class);
    }
    public function donorItems(): HasMany
    {
        return $this->hasMany(donorItem::class);
    }
    public function transactionWarehouseItem():HasMany
    {
        return $this->hasMany(transactionItem::class);
    }
//    public function warehouse(): BelongsToMany
//    {
//        return $this->belongsToMany(Warehouse::class,'warehouse_items');
//    }
    public function ctns():HasMany
    {
        return $this->hasMany(Ctn::class);
    }
}
