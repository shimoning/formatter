<?php

use PHPUnit\Framework\TestCase;
use Shimoning\Formatter\Link;

class LinkTest extends TestCase
{
    public function test_getHtml()
    {
        // URLのみ
        $result = Link::getHtml('https://example.com');
        $this->assertEquals('<a href="https://example.com" target="_blank">https://example.com</a>', $result);

        // URLとテキスト
        $result = Link::getHtml('https://example.com', 'Example');
        $this->assertEquals('<a href="https://example.com" target="_blank">Example</a>', $result);

        // URL、テキスト、属性
        $result = Link::getHtml('https://example.com', 'Example', ['class' => 'link']);
        $this->assertEquals('<a href="https://example.com" class="link" target="_blank">Example</a>', $result);

        // 属性が文字列の場合
        $result = Link::getHtml('https://example.com', 'Example', 'class="link" data-info="value"');
        $this->assertEquals('<a href="https://example.com" class="link" data-info="value" target="_blank">Example</a>', $result);

        // target属性が指定されている場合は上書きされないことを確認
        $result = Link::getHtml('https://example.com', 'Example', ['target' => '_self']);
        $this->assertEquals('<a href="https://example.com" target="_self">Example</a>', $result);

        // 内部リンクの場合は target が _self になることを確認
        $result = Link::getHtml('/internal-path', 'Internal');
        $this->assertEquals('<a href="/internal-path" target="_self">Internal</a>', $result);

        // 内部リンクでも target が指定されている場合は上書きされないことを確認
        $result = Link::getHtml('/internal-path', 'Internal', ['target' => '_blank']);
        $this->assertEquals('<a href="/internal-path" target="_blank">Internal</a>', $result);

        // HTMLエスケープの確認
        $result = Link::getHtml('https://example.com?param=<script>', 'Example & Test', ['data-info' => 'Value "with" quotes']);
        $this->assertEquals('<a href="https://example.com?param=%3Cscript%3E" data-info="Value &quot;with&quot; quotes" target="_blank">Example &amp; Test</a>', $result);

        // GET クエリのエスケープの確認
        $result = Link::getHtml('https://example.com?key1=value1&key2=value2+plus&key3=value3%20space', 'Example');
        $this->assertEquals('<a href="https://example.com?key1=value1&key2=value2+plus&key3=value3+space" target="_blank">Example</a>', $result);

        // GET クエリがあり text を省略した時
        $result = Link::getHtml('https://example.com?key1=value1&key2=value2');
        $this->assertEquals('<a href="https://example.com?key1=value1&key2=value2" target="_blank">https://example.com?key1=value1&amp;key2=value2</a>', $result);
    }

    public function test_buildUrl()
    {
        // パースして復元する
        $testUrl = 'https://user:pass@example.com:8080/path/to/resource?key=value#section';
        $parsedUrl = parse_url($testUrl);
        $result = Link::buildUrl($parsedUrl);
        $this->assertEquals($testUrl, $result);

        // 基本的なURL
        $result = Link::buildUrl(['scheme' => 'https', 'host' => 'example.com']);
        $this->assertEquals('https://example.com', $result);

        // プロトコル省略URL
        $result = Link::buildUrl(['host' => 'example.com']);
        $this->assertEquals('//example.com', $result);

        // ユーザー情報を含むURL
        $result = Link::buildUrl(['scheme' => 'https', 'host' => 'example.com', 'user' => 'user', 'pass' => 'pass']);
        $this->assertEquals('https://user:pass@example.com', $result);

        // ポート番号を含むURL
        $result = Link::buildUrl(['scheme' => 'https', 'host' => 'example.com', 'port' => 8080]);
        $this->assertEquals('https://example.com:8080', $result);

        // パスを含むURL
        $result = Link::buildUrl(['scheme' => 'https', 'host' => 'example.com', 'path' => '/path/to/resource']);
        $this->assertEquals('https://example.com/path/to/resource', $result);

        // クエリとフラグメントを含むURL
        $result = Link::buildUrl(['scheme' => 'https', 'host' => 'example.com', 'query' => 'key=value', 'fragment' => 'section']);
        $this->assertEquals('https://example.com?key=value#section', $result);
    }

    public function test_parseAttributes()
    {
        // 文字列の属性
        $result = Link::parseAttributes('class="link" data-info="value"');
        $this->assertEquals(['class' => 'link', 'data-info' => 'value'], $result);

        // シングルクォートの属性
        $result = Link::parseAttributes("class='link' data-info='value'");
        $this->assertEquals(['class' => 'link', 'data-info' => 'value'], $result);

        // 文字列の属性で値にスペースがある場合
        $result = Link::parseAttributes('class="link with spaces" data-info="value with spaces"');
        $this->assertEquals(['class' => 'link with spaces', 'data-info' => 'value with spaces'], $result);
    }

    public function test_getTarget()
    {
        // CLI環境でのデフォルトのベースURLに対する外部リンクの判定
        $result = Link::getTarget('http://localhost');
        $this->assertEquals('_self', $result);

        // 外部リンク
        $result = Link::getTarget('https://example.com');
        $this->assertEquals('_blank', $result);

        // 同一ドメインのURL
        $result = Link::getTarget('https://example.com/hoge', 'https://example.com/');
        $this->assertEquals('_self', $result);

        // false を指定
        $result = Link::getTarget('http://localhost', false);
        $this->assertEquals('_blank', $result);
        $result = Link::getTarget('http://example.com', false);
        $this->assertEquals('_blank', $result);
        $result = Link::getTarget('/path/to', false);
        $this->assertEquals('_self', $result);

        // 省略されたプロトコルの外部リンク
        $result = Link::getTarget('//example.com');
        $this->assertEquals('_blank', $result);

        // 内部リンク: ルート絶対パス
        $result = Link::getTarget('/absolute-path');
        $this->assertEquals('_self', $result);

        // 内部リンク: 相対ディレクトリパス
        $result = Link::getTarget('./internal-path');
        $this->assertEquals('_self', $result);

        // 内部リンク: 相対ファイルパス
        $result = Link::getTarget('./internal.css');
        $this->assertEquals('_self', $result);
    }

    public function test_isExternalHref()
    {
        // CLI環境でのデフォルトのベースURLに対する外部リンクの判定
        $result = Link::isExternalHref('http://localhost');
        $this->assertFalse($result);

        // シンプルなURL
        $result = Link::isExternalHref('https://example.com');
        $this->assertTrue($result);

        // シンプルなURL(一致)
        $result = Link::isExternalHref('https://example.com', 'https://example.com/');
        $this->assertFalse($result);

        // falseを指定して比較しない場合はURLだけで判定されることを確認
        $result = Link::isExternalHref('http://localhost', false);
        $this->assertTrue($result);

        // 省略されたプロトコルのURL
        $result = Link::isExternalHref('//example.com');
        $this->assertTrue($result);

        // 内部リンク: ルート絶対パス
        $result = Link::isExternalHref('/absolute-path');
        $this->assertFalse($result);

        // 内部リンク: 相対ディレクトリパス
        $result = Link::isExternalHref('./internal-path');
        $this->assertFalse($result);

        // 内部リンク: 相対ファイルパス
        $result = Link::isExternalHref('./internal.css');
        $this->assertFalse($result);
    }

    public function test_isSiteHref()
    {
        // シンプルなURL
        $result = Link::isSiteHref('https://example.com');
        $this->assertTrue($result);

        // 省略されたプロトコルのURL
        $result = Link::isSiteHref('//example.com');
        $this->assertTrue($result);

        // 内部リンク: ルート絶対パス
        $result = Link::isSiteHref('/absolute-path');
        $this->assertFalse($result);

        // 内部リンク: 相対ディレクトリパス
        $result = Link::isSiteHref('./internal-path');
        $this->assertFalse($result);

        // 内部リンク: 相対ファイルパス
        $result = Link::isSiteHref('./internal.css');
        $this->assertFalse($result);
    }

    public function test_isMailHref()
    {
        // 無効なURL
        $result = Link::isMailHref('invalid-url');
        $this->assertFalse($result);

        // 通常の外部リンク
        $result = Link::isMailHref('https://example.com');
        $this->assertFalse($result);

        // メールリンク
        $result = Link::isMailHref('mailto:example@example.com');
        $this->assertTrue($result);
    }

    public function test_isTelHref()
    {
        // 無効なURL
        $result = Link::isTelHref('invalid-url');
        $this->assertFalse($result);

        // 通常の外部リンク
        $result = Link::isTelHref('https://example.com');
        $this->assertFalse($result);

        // 電話リンク
        $result = Link::isTelHref('tel:+1234567890');
        $this->assertTrue($result);
    }

    public function test_guessBaseUrl()
    {
        // CLI環境では http://localhost を返す (phpunit.xml)
        $result = Link::guessBaseUrl();
        $this->assertEquals('http://localhost', $result);
    }
}
