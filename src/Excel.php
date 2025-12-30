<?php

namespace Shimoning\Formatter;

class Excel
{
    public const BASE = 26;
    public const CODE = 65;

    /**
     * 列番号をアルファベットに変換する
     * @param int $index
     * @return string|false
     */
    public static function alphabet(int $index)
    {
        if ($index < 1) {
            return false;
        }
        $alphabets = '';

        while ($index > 0) {
            $index--;

            $alphabets = \chr($index % self::BASE + self::CODE) . $alphabets;
            $index = floor($index / self::BASE);
        }

        return $alphabets;
    }

    /**
     * 列のアルファベットを列番号に変換する
     *
     * @param string $alphabets
     * @return int|false
     */
    public static function index(string $alphabets)
    {
        if (empty($alphabets) || ! preg_match('/^[a-zA-z]+$/', $alphabets)) {
            return false;
        }
        $alphabets = \strtoupper($alphabets);

        $index = 0;
        $length = \strlen($alphabets);
        for ($i = 0; $i < $length; $i++) {
            $index += (\ord($alphabets[$length - $i - 1]) - self::CODE + 1) * \pow(self::BASE, $i);
        }

        return $index;
    }
}
