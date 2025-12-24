<?php

namespace Shimoning\Formatter;

class Text
{
    /**
     * マルチバイト対応で、文字列の前後から空白を取り除く
     *
     * @param string $string
     * @return string
     */
    static public function trim(string $string): string
    {
        return \preg_replace(
            '/\A[\p{Cc}\p{Cf}\p{Z}]++|[\p{Cc}\p{Cf}\p{Z}]++\z/u',
            '',
            $string,
        );
    }

    /**
     * マルチバイト対応で、文字列を空白文字で区切る
     *
     * @param string $string
     * @return string[]
     */
    static public function splitBySpace(string $string): array
    {
        return \preg_split(
            '/[\p{Cc}\p{Cf}\p{Z}]++/u',
            $string,
            -1,
            PREG_SPLIT_NO_EMPTY,
        );
    }

    /**
     * マルチバイト対応で、文字列を空白文字で区切る（エイリアス）
     *
     * @see Text::splitBySpace()
     *
     * @param string $string
     * @return string[]
     */
    static public function explodeBySpace(string $string): array
    {
        return self::splitBySpace($string);
    }
}
