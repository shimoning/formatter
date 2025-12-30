<?php

namespace Shimoning\Formatter;

class Sql
{
    /**
     * SQL で部分検索に使用する文字列をサニタイズ
     *
     * @param string $text
     * @return string
     */
    public static function sanitizeTextForSearchQuery(string $text): string
    {
        return \str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\%', '\_'],
            $text,
        );
    }
}
