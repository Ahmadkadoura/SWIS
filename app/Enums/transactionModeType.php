<?php

namespace App\Enums;

enum transactionModeType :  int
{
    //for transaction IN
    case Received_from_donors = 1;
    case Received_from_warehouses= 2;
    case Return_from_Distribution_point= 3;


    //for transaction OUT
    case out_for_Distribution_point= 4;
    case out_for_warehouses= 5;
    case Return_for_donors=6;
    case loss= 7;
    case damage= 8;
}
