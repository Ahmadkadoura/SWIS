<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\Translatable\HasTranslations;

class Branch extends Model implements Searchable
{
    use HasFactory, SoftDeletes, HasTranslations;
    public $translatable = ['name', 'address'];
    protected $fillable = [
        'id',
        'name',
        'code',
        'parent_id',
        'phone',
        'address',
    ];

    public function getSearchResult(): SearchResult
    {
        $url = route('branches.search', $this->slug);
        return new SearchResult($this, $this->name, $url);
    }
    public function warehouse(){
        return $this->hasMany(Warehouse::class);
    }
    public function donorItem(){
        return $this->hasMany(DonorItem::class);
    }
    public function parentBranch():BelongsTo
    {
        return $this->belongsTo(Branch::class, 'parent_id');
    }
}
