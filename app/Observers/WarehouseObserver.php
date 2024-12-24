<?php

namespace App\Observers;

use App\Models\Warehouse;

class WarehouseObserver
{
    /**
     * Handle the Warehouse "created" event.
     */
    public function created(Warehouse $warehouse): void
    {
        $var = $warehouse->getTranslation('name', 'en');
        $code = substr($var, 0, 4) . (1000+ $warehouse->id);
        $warehouse->code = $code;
        $warehouse->save();
    }

}
