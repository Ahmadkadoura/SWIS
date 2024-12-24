<?php

namespace App\Observers;

use App\Models\Branch;

class BranchObserver
{
    /**
     * Handle the Branch "created" event.
     */
    public function created(Branch $branch): void
    {
        $var = $branch->getTranslation('name', 'en');
        $code = substr($var, 0, 4) . (30000 + $branch->id);
        $branch->code = $code;
        $branch->save();
    }

}
