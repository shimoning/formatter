<?php

namespace Shimoning\Formatter;

class Number
{
    /**
     * 数字的な文字列からカンマを取り除く
     *
     * @param string $numeric
     * @param string $comma
     * @return int
     */
    public static function removeComma(string $numeric, $comma = ','): int
    {
        return (int)(\str_replace($comma, '', $numeric));
    }

    /**
     * 標準関数 number_format のラッパー
     *
     * @param int|string $numeric
     * @param string $attachComma
     * @param string $detachComma
     * @return string
     */
    public static function numberFormat($numeric, $attachComma = ',', $detachComma = ','): string
    {
        try {
            $formatted = \number_format(Number::removeComma($numeric, $detachComma));
            return $attachComma === ',' ? $formatted : \str_replace(',', $attachComma, $formatted);
        } catch (\Exception $e) {
            return $numeric;
        }
        return '';
    }
}
