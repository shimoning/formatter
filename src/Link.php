<?php

namespace Shimoning\Formatter;

class Link
{
    /**
     * URLをHTMLのリンクタグに変換する
     *
     * @param string $url
     * @param string $text
     * @param string|array $attributes
     * @param string|false $baseUrl 内部リンクの基準となるURL。省略した場合は現在のURLが使用される。false の場合は target の自動判定も行わない
     * @return string
     */
    public static function getHtml(string $url, string $text = '', $attributes = [], $baseUrl = ''): string
    {
        if (empty($text)) {
            $text = $url;
        }

        $attributeString = '';
        if (empty($attributes)) {
            $attributes = [];
        }
        if (\is_string($attributes)) {
            $attributes = self::parseAttributes($attributes);
        }
        if (!isset($attributes['target']) && $baseUrl !== false) {
            $attributes['target'] = self::getTarget($url, $baseUrl);
        }
        foreach ($attributes as $key => $value) {
            $attributeString .= ' ' . \htmlspecialchars($key) . '="' . \htmlspecialchars($value) . '"';
        }

        $parsedUrl = \parse_url($url);
        $builtUrl = self::buildUrl($parsedUrl);

        return '<a href="' . $builtUrl . '"' . $attributeString . '>' . \htmlspecialchars($text) . '</a>';
    }

    /**
     * parse_url の結果を元にURLを構築する
     *
     * @param array{scheme?:string, host?:string, port?:int, user?:string, pass?:string, query?:string, path?:string, fragment?:string} $elements
     * @return string
     */
    public static function buildUrl(array $elements): string
    {
        $url = '';
        if (isset($elements['host'])) {
            $url .= isset($elements['scheme']) ? $elements['scheme'] . '://' : '//';
            if (isset($elements['user'])) {
                $url .= $elements['user'];
                if (isset($elements['pass'])) {
                    $url .= ':' . $elements['pass'];
                }
                $url .= '@';
            }
            $url .= $elements['host'];
            if (isset($elements['port'])) {
                $url .= ':' . $elements['port'];
            }
        }
        if (isset($elements['path'])) {
            $url .= $elements['path'];
        }
        if (isset($elements['query'])) {
            \parse_str($elements['query'], $queries);
            $url .= '?' . \http_build_query($queries);
        }
        if (isset($elements['fragment'])) {
            $url .= '#' . $elements['fragment'];
        }
        return $url;
    }

    /**
     * 属性文字列を解析して連想配列に変換する
     *
     * @param string $attributeString
     * @return array
     */
    public static function parseAttributes(string $attributeString): array
    {
        // ダミーのHTMLを作成して属性を解析する
        $dom = new \DOMDocument();
        $html = '<span ' . $attributeString . '></span>';
        @$dom->loadHTML($html);
        $tag = $dom->getElementsByTagName('span')->item(0);
        $attributes = [];
        foreach ($tag->attributes as $attr) {
            $attributes[$attr->name] = $attr->value;
        }
        return $attributes;
    }

    /**
     * URLのターゲットを取得する
     * 外部リンクの場合は _blank、内部リンクの場合は _self を返す
     *
     * @param string $url
     * @param string|false $baseUrl 内部リンクの基準となるURL。省略した場合は現在のURLが使用される。 false の場合は比較しない
     * @return string
     */
    public static function getTarget(string $url, $baseUrl = ''): string
    {
        if (self::isExternalHref($url, $baseUrl)) {
            return '_blank';
        }
        return '_self';
    }

    /**
     * URLが外部リンクかどうかを判定する
     *
     * @param string $url
     * @param string|false $baseUrl 内部リンクの基準となるURL。省略した場合は現在のURLが使用される。 false の場合は $url だけで判定する
     * @return bool
     */
    public static function isExternalHref(string $url, $baseUrl = ''): bool
    {
        $isSiteHref = self::isSiteHref($url);
        if ($baseUrl === false) {
            return $isSiteHref;
        }

        $baseUrl = $baseUrl ?: self::guessBaseUrl();
        return $isSiteHref && parse_url($url, PHP_URL_HOST) !== parse_url($baseUrl, PHP_URL_HOST);
    }

    /**
     * hrefがサイトURIかどうかを判定する
     * https://, http://, // で始まるURLはサイトURIとみなす
     *
     * @param string $url
     * @return bool
     */
    public static function isSiteHref(string $url): bool
    {
        return \preg_match('/^(https?:)?\/\//i', $url) === 1;
    }

    /**
     * メールリンクかどうかを判定する
     * mailto: で始まるURLはメールリンクとみなす
     *
     * @param string $url
     * @return bool
     */
    public static function isMailHref(string $url): bool
    {
        return \preg_match('/^mailto:/i', $url) === 1;
    }

    /**
     * 電話リンクかどうかを判定する
     * tel: で始まるURLは電話リンクとみなす
     *
     * @param string $url
     * @return bool
     */
    public static function isTelHref(string $url): bool
    {
        return \preg_match('/^tel:/i', $url) === 1;
    }

    /**
     * 現在のベースURLを推測する
     *
     * @return string
     */
    public static function guessBaseUrl(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    }
}
