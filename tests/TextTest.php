<?php

use PHPUnit\Framework\TestCase;
use Shimoning\Formatter\Text;

class TextTest extends TestCase
{
    public function test_trim()
    {
        // 先頭 & 最後半角スペース
        $result = Text::trim(' a23 あああ ');
        $this->assertEquals('a23 あああ', $result);

        // 先頭全角スペース, 最後半角スペース
        $result = Text::trim('　a23 あああ ');
        $this->assertEquals('a23 あああ', $result);

        // 先頭半角スペース, 最後全角スペース
        $result = Text::trim(' a23 あああ 　');
        $this->assertEquals('a23 あああ', $result);

        // 先頭 & 最後全角スペース
        $result = Text::trim('　a23 あああ 　');
        $this->assertEquals('a23 あああ', $result);

        $text = <<< HERE
　a23
　い
あああ　
HERE;
        $result = Text::trim($text);
        $expected = <<< EXPECTED
a23
　い
あああ
EXPECTED;
        $this->assertEquals($expected, $result);
    }

    public function test_splitBySpace()
    {
        // 半角スペース
        $result = Text::splitBySpace(' a23 あああ ');
        $this->assertEquals(['a23', 'あああ'], $result);

        // 全角スペース
        $result = Text::splitBySpace('　a23　あああ　');
        $this->assertEquals(['a23', 'あああ'], $result);

        // 全角スペース+半角スペース
        $result = Text::splitBySpace('　a23 あああ　');
        $this->assertEquals(['a23', 'あああ'], $result);

        // タブ
        $result = Text::splitBySpace("\ta23\tあああ\t");
        $this->assertEquals(['a23', 'あああ'], $result);

        // 改行
        $result = Text::splitBySpace("\na23\nあああ\n");
        $this->assertEquals(['a23', 'あああ'], $result);

        // 改行
        $result = Text::splitBySpace("\ra23\rあああ\r");
        $this->assertEquals(['a23', 'あああ'], $result);
    }

    public function test_explodeBySpace()
    {
        // 半角スペース
        $result = Text::explodeBySpace(' a23 あああ ');
        $this->assertEquals(['a23', 'あああ'], $result);

        // 全角スペース
        $result = Text::explodeBySpace('　a23　あああ　');
        $this->assertEquals(['a23', 'あああ'], $result);

        // 全角スペース+半角スペース
        $result = Text::explodeBySpace('　a23 あああ　');
        $this->assertEquals(['a23', 'あああ'], $result);

        // タブ
        $result = Text::explodeBySpace("\ta23\tあああ\t");
        $this->assertEquals(['a23', 'あああ'], $result);

        // 改行
        $result = Text::explodeBySpace("\na23\nあああ\n");
        $this->assertEquals(['a23', 'あああ'], $result);

        // 改行
        $result = Text::explodeBySpace("\ra23\rあああ\r");
        $this->assertEquals(['a23', 'あああ'], $result);
    }
}
