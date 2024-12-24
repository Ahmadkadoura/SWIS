<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $var = $user->getTranslation('name', 'en');
        $code = substr($var, 0, 4) . (2000 + $user->id);
        $user->code = $code;
        $user->save();
    }

}
