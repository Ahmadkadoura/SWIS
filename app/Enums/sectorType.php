<?php

namespace App\Enums;

enum sectorType: int
{
    case WASH = 1;
    case NFIs_Shelters = 2;
    case COMPLETEDFood_Agriculture = 3;
    case Health = 4;
    case Nutrition = 5;
    case Protection = 6;
    case Education = 7;
    case Livelihoods = 8;

    // Add a method to get the localized name
//    public function localizedName(): string
//    {
//        return match($this) {
//            self::WASH => 'WASH',
//            self::NFIs_Shelters => 'NFIs_Shelters',
//            self::COMPLETEDFood_Agriculture => 'COMPLETEDFood_Agriculture',
//            self::Health => 'Health',
//            self::Nutrition => 'Nutrition',
//            self::Protection => 'Protection',
//            self::Education => 'Education',
//            self::Livelihoods => 'Livelihoods',
//        };
//    }
}
