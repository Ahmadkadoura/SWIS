<?php

namespace App\Enums;

enum sourceType: string
{
    case donor = 'App\Models\User';
    case keeper = 'App\Models\Warehouse';

}

