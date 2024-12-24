<?php

namespace App\Http\Repositories;

use App\Models\Warehouse;
use App\Models\WarehouseItem;

class warehouseItemRepository extends baseRepository
{
    public function __construct(WarehouseItem $model)
    {
        parent::__construct($model);
    }

}
