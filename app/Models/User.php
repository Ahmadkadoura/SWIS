<?php

namespace App\Models;

// use Illuminate\Contracts\Api\MustVerifyEmail;
use App\Enums\userType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use PhpParser\Node\Scalar\String_;
use PHPUnit\Util\Filesystem;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;
use Spatie\Translatable\HasTranslations;

class User extends Authenticatable implements Searchable
{
    use HasApiTokens, HasFactory, Notifiable, hasRoles, SoftDeletes, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users'; // Specify the correct table name

    public $translatable = ['name'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'code',
        'contact_email',
        'photo',
        'type',
        'first_login',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'type' => userType::class,

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function getSearchResult(): SearchResult
    {
        $url = route('users.search', $this->slug);
        return new SearchResult($this, $this->name, $url);
    }
    public function warehouse()
    {
        return $this->hasOne(Warehouse::class);
    }

    public function sourceTransactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'sourceable');
    }
    public function destinationTransactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'distianationable');
    }

    public static function getDisk()
    {
        return Storage::disk('users');
    }
    public function imageUrl(string $fieldName)
    {
        if(str_starts_with($this->$fieldName,'http')) {
            return $this->$fieldName;
        }else{
            return $this->$fieldName ? self::getDisk()->url($this->$fieldName) : null;
        }
    }
}
