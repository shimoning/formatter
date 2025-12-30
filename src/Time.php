<?php

namespace Shimoning\Formatter;

class Time
{
    /**
     * 数値を時間フォーマットに変換
     *
     * @param int|string $number
     * @param string $separator
     * @return string
     */
    public static function number2clock($number, $separator = ':'): string
    {
        $t0 = \floor($number / 60);
        $t1 = $number % 60;
        return $t0 . $separator . \str_pad($t1, 2, 0, STR_PAD_LEFT);
    }

    /**
     * 時間フォーマットの文字列を数値に変換
     *
     * @param string|int $clock
     * @param string $separator
     * @return int
     */
    public static function clock2number($clock, $separator = ':'): int
    {
        $times = \explode($separator, $clock);
        return \count($times) >= 2
            ? $times[0] * 60 + $times[1] * 1
            : 0;
    }
}
