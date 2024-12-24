<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\Translatable\HasTranslations;

class Driver extends Model implements Searchable
{
  use HasFactory, SoftDeletes, HasTranslations;

  public $translatable = ['name', 'transportation_company_name'];

  protected $fillable = [
    'id',
    'name',
    'vehicle_number',
    'national_id',
    'transportation_company_name',
    'phone',
  ];

  public function getSearchResult(): SearchResult
  {
    $url = route('drivers.search', $this->slug);
    return new SearchResult($this, $this->name, $url);
  }

  public function transactions()
  {
    return $this->belongsToMany(Transaction::class);
  }

}
