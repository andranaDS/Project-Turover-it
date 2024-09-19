<?php

namespace App\Core\Util;

class Dates
{
    public static function currentWeek(): \DateTime
    {
        return (new \DateTime())
            ->setTimestamp(strtotime('next monday -1 week', strtotime('this sunday')))
        ;
    }

    public static function lastWeek(int $count = 0): \DateTime
    {
        return self::currentWeek()
            ->modify(sprintf('- %s weeks', $count + 1))
        ;
    }
}
