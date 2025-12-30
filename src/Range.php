<?php

namespace Shimoning\Formatter;

class Range
{
    /**
     * n番台の最初の値
     *
     * @param int $number
     * @param int $digits
     * @return int
     */
    public static function lowerBound($number, $digits): int
    {
        return (int)\str_pad($number, $digits, 0, STR_PAD_RIGHT);
    }

    /**
     * n番台の最後の値
     *
     * @param int $number
     * @param int $digits
     * @return int
     */
    public static function upperBound($number, $digits): int
    {
        return (int)\str_pad($number, $digits, 9, STR_PAD_RIGHT);
    }
}
