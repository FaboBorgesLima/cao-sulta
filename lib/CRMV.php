<?php

namespace Lib;

use Lib\StringUtils;
use Lib\Timestamp;

class CRMV
{
    public const MIN_YEAR = 1968;

    public static function isValid(string $str): bool
    {

        if (strlen($str) != 7 || !StringUtils::isNumeric($str)) {
            return false;
        }

        $year = static::getYear($str);

        return ($year >= static::MIN_YEAR && Timestamp::now()->getYear() >= $year);
    }

    private static function getYear(string $crmv): int
    {
        return (int) substr($crmv, 0, 4);
    }
}
