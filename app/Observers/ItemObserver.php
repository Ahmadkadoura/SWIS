<?php

namespace App\Observers;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemObserver
{
    /**
     * Handle the Item "created" event.
     */
    public function created(Item $item): void
    {
        $user = Auth::check() ? Auth::user()->getTranslation('name', 'en') : 'defaultUser';
        $var = $item->getTranslation('name', 'en');
        $code = substr($var, 0, 4) .substr($user, 0, 4). ( $item->id);
        $item->code = $code;
        $item->save();
    }
}
