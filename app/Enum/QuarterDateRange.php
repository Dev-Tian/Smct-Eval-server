<?php

namespace App\Enum;

use Carbon\Carbon;

enum QuarterDateRange
{
    case Q1 ;
    case Q2 ;
    case Q3 ;
    case Q4 ;

    public function range()
    {
        $currentYear = now()->year;
        $lastYear = now()->subYear()->year;

        return match ($this)
        {
            self::Q1 =>
                [
                    Carbon::create($currentYear,1,1)->startOfMonth(),
                    Carbon::create($currentYear,3,1)->endOfMonth()
                ],

            self::Q2 =>
                [
                    Carbon::create($currentYear,4,1)->startOfMonth(),
                    Carbon::create($currentYear,6,1)->endOfMonth()
                ],

            self::Q3 =>
                [
                    Carbon::create($currentYear,7,1)->startOfMonth(),
                    Carbon::create($currentYear,9,1)->endOfMonth()
                ],

            self::Q4 =>
                [
                    Carbon::create($lastYear,10,1)->startOfMonth(),
                    Carbon::create($lastYear,12,1)->endOfMonth()
                ],
        };

    }
}
