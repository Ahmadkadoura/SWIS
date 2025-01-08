<?php

namespace App\Models;

use App\Enums\sourceType;
use App\Enums\transactionModeType;
use App\Enums\transactionType;
use App\Enums\transactionStatusType;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\Translatable\HasTranslations;

class Transaction extends Model implements Searchable
{
    use HasFactory, SoftDeletes, HasTranslations;

    public $translatable = ['notes'];
    protected $fillable = [
        'parent_id',
        'is_convoy',
        'notes',
        'code',
        'sourceable_type',
        'sourceable_id',
        'destinationable_type',
        'destinationable_id',
        'status',
        'date',
        'transaction_type',
        'transaction_mode_type',
        'waybill_num',
        'waybill_img',
        'qr_code',

    ];

    protected $casts = [
        'transaction_type'=>transactionType::class ,
        'transaction_mode_type'=>transactionModeType::class ,
        'sourceable_type'=>sourceType::class,
        'destinationable_type'=>sourceType::class,
        'status'=>transactionStatusType::class,
    ];

    public function getSearchResult(): SearchResult
    {
        $url = route('transactions.search', $this->slug);
        return new SearchResult($this, $this->code, $url);
    }
    public function scopeDate(Builder $query, $startDate , $endDate): Builder
    {
        return $query->whereBetween('date', [Carbon::parse($startDate),Carbon::parse($endDate)->addDay()]);
    }

    public function driverTransaction()
    {
        return $this->hasMany(transactionDriver::class);
    }
       public function transactionItem()
    {
        return $this->hasMany(TransactionItem::class);
    }
    public function parentTransaction():BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'parent_id');
    }
    public function childTransactions():HasMany
    {
        return $this->hasMany(Transaction::class, 'parent_id');
    }

    public static function getDisk()
    {
        return Storage::disk('transactions');
    }
    public function imageUrl(string $fieldName)
    {
        if(str_starts_with($this->$fieldName,'http')) {
            return $this->$fieldName;
        }else{

            return $this->$fieldName ? self::getDisk()->url($this->$fieldName) : null;
        }
    }
    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function destinationable(): MorphTo
    {
        return $this->morphTo();
    }
}
