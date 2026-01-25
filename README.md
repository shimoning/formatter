# formatter
各種変換ユーティリティ

## Install

利用するプロジェクトの `composer.json` に設定を追加する。

その後以下で composer でインストールを実行する。
```bash
composer require shimoning/formatter
```

### gitコマンドを使って追加する
```bash
composer config repositories.shimoning/formatter vcs https://github.com/shimoning/formatter.git
```

### 手動でcomposer.jsonを編集して追加する
```composer.json
"repositories": {
    "formatter": {
        "type": "vcs",
        "url": "https://github.com/shimoning/formatter.git"
    }
},
```

## Support

PHP 7.3 以上
要: `php-dom`

## Usage

### 時間系
#### number2clock
**数値を `n:mm` 形式にする**

数値に `分` を入れれば `h:mm` として、`秒` を入れれば `m:ss` として利用できる。
`:` より前の値は 3桁以上になりうる。

```php
Time::number2clock(100); // -> 1:40
```

第2引数には時間を分けるための文字を設定可能。
デフォルトでは `:` となっている。

```php
Time::number2clock(100, '-'); // -> 1-40
```

#### clock2number
**`n:mm` 形式の文字列を数値にする**

`number2clock` の逆。

```php
Time::clock2number('1:40'); // -> 100
```

第2引数には時間を分けるための文字を設定可能。
デフォルトでは `:` となっている。

```php
Time::clock2number('1-40', '-'); // -> 100
```


### 数値
#### removeComma
数字的な文字列からカンマを取り除く。

```php
Number::removeComma('123,456'); // -> 123456
```

第2引数には、カンマ扱いする文字を設定可能。
デフォルトでは `,` となっている。

```php
Number::removeComma('222 333', ' '); // -> 222333
```

#### numberFormat
標準関数 `number_format` のラッパー。

```php
Number::numberFormat(123456); // -> 123,456
```

第2引数には、区切り文字として付与する文字を設定可能。
デフォルトでは `,` となっている。

```php
Number::numberFormat(222333, ' '); // -> 222 333
```

第3引数には、削除すべき区切り文字を設定可能。
デフォルトでは `,` となっている。

```php
Number::numberFormat('111=222', ' ', '='); // -> 111 222
```


### SQL関連
#### sanitizeTextForSearchQuery
**SQLのサニタイザ**

前方一致や後方一致を安全に行うための文字列サニタイザ。

```php
Sql::sanitizeTextForSearchQuery('%test'); // -> \%test
```


### 文字列系
#### trim
**マルチバイト対応 trim**

マルチバイト対応で、文字列の前後から空白を取り除く。

```php
// [全角スペース]a23[半角スペース]あああ
Text::trim('　a23 あああ '); // -> a23 あああ
```

#### splitBySpace/explodeBySpace
**マルチバイト対応で空白で文字列を配列にする**

マルチバイト対応のスペース限定 `explode` 。
末尾のスペースは無視する。

```php
// [全角スペース]a23[半角スペース]あああ
Text::splitBySpace('　a23 あああ　') // -> ['a23', 'あああ']

// エイリアス
Text::explodeBySpace('　a23 あああ　') // -> ['a23', 'あああ']
```


### Excel関連
#### alphabet
**列番号をアルファベットに変換する**

変換できない場合は false を返す。

```php
Excel::alphabet(0); // -> false
Excel::alphabet(1); // -> A
Excel::alphabet(27); // -> AA
```

#### index
**列のアルファベットを列番号に変換する**

変換できない場合は false を返す。

```php
Excel::index('エラー'); // -> false
Excel::index('A');; // -> 1
Excel::index('AA'); // -> 27
```


### 範囲
#### lowerBound
**n番台の最初の値を取得する**

- 第1引数は、1桁目の数字。
- 第2引数は、桁数。

```php
Range::lowerBound(1, 3); // -> 100
```

#### upperBound
**n番台の最後の値を取得する**

第1引数は、1桁目の数字。
第2引数は、桁数。

```php
Range::upperBound(1, 3); // -> 199
```


### リンク
(フォーマットというよりはユーティリティ群)

#### getHtml
**URLをHTMLのリンクタグに変換する**

- 第1引数は、href の値
- 第2引数は、リンクのテキスト。省略すると URL が表示される
- 第3引数は、属性の配列もしくは文字列
- 第4引数は、基本となるURLを設定。初期では現在のURLが自動で使われる

```php
// 第4引数を指定しない場合、プロトコルから始まる場合は自動で target="_blank" を付与する
Link::getHtml('https://example.com'); // -> <a href="https://example.com" target="_blank">https://example.com</a>;
Link::getHtml('https://example.com', 'Example'); // -> <a href="https://example.com" target="_blank">Example</a>
Link::getHtml('http://example.com'); // -> <a href="http://example.com" target="_blank">http://example.com</a>;
Link::getHtml('//example.com'); // -> <a href="//example.com" target="_blank">//example.com</a>;

// 第3引数 (クラスやdata属性やその他)
Link::getHtml('https://example.com', 'Example', ['class' => 'link']); // -> <a href="https://example.com" class="link" target="_blank">Example</a>
Link::getHtml('https://example.com', 'Example', 'class="link"'); // -> <a href="https://example.com" class="link" target="_blank">Example</a>

// 第4引数
Link::getHtml('https://example.com/hoge', 'Example', '', 'https://example.com'); // -> <a href="https://example.com/hoge" target="_self">Example</a>
// false を指定すると target の属性を自動で付与しない
Link::getHtml('https://example.com/hoge', 'Example', '', false); // -> <a href="https://example.com/hoge">Example</a>
```

#### buildUrl
**parse_url の結果を元にURLを構築する**

```php
Link::buildUrl(['scheme' => 'https', 'host' => 'example.com', 'user' => 'user', 'pass' => 'pass']); // -> https://user:pass@example.com
```

#### parseAttributes
**属性文字列を連想配列にする**

```php
Link::parseAttributes('class="link" data-info="value"'); // -> ['class' => 'link', 'data-info' => 'value']
```

#### getTarget
**Aタグの target の値を取得する**

- 第1引数は、href の値
- 第2引数は、基本となるURLを設定。初期では現在のURLが自動で使われる。

```php
// ルートや相対パスなどの場合は _self
Link::getTarget('/path/to'); // -> '_self'
Link::getTarget('./path/to'); // -> '_self'

// 第2引数を省略すると、アクセスしたホスト名が比較される (想定: http://localhost)
Link::getTarget('https://example.com'); // -> '_blank'

// 第2引数を指定すると
Link::getTarget('https://example.com/hoge', 'https://example.com'); // -> '_self'

// 第2引数にfalseを指定すると、プロトコルが指定されていれば全て _blank になる
Link::getTarget('https://example.com/hoge', false); // -> '_blank'
Link::getTarget('http://localhost', false); // -> '_blank'
```

#### isExternalHref
**URLが外部リンクかどうかを判定する**

- 第1引数は、href の値
- 第2引数は、基本となるURLを設定。初期では現在のURLが自動で使われる。

```php
// ルートや相対パスなどの場合は内部認定
Link::isExternalHref('/path/to'); // -> false
Link::isExternalHref('./path/to'); // -> false

// 第2引数を省略すると、アクセスしたホスト名が比較される (想定: http://localhost)
Link::isExternalHref('http://localhost'); // -> false
Link::isExternalHref('https://example.com'); // -> true

// 第2引数を指定すると
Link::isExternalHref('https://example.com/hoge', 'https://example.com'); // -> false

// 第2引数にfalseを指定すると、プロトコルが指定されていれば全て外部リンク扱い
Link::isExternalHref('https://example.com/hoge', false); // -> true
Link::isExternalHref('http://localhost', false); // -> true
```

#### isSiteHref
**hrefがサイトURIかどうかを判定する**
https://, http://, // で始まるURLはサイトURIとみなす。
実在するかどうかは検証しない。

```php
// パス
Link::isSiteHref('/path/to'); // -> false
Link::isSiteHref('./path/to'); // -> false

// プロトコルあり
Link::isSiteHref('https://example.com'); // -> true
Link::isSiteHref('//example.com'); // -> true

// それ以外の書式リンク
Link::isSiteHref('mailto:example@example.com'); // -> false
```

#### isMailHref
**メールリンクかどうかを判定する**
mailto: で始まるURLはメールリンクとみなす。

```php
Link::isMailHref('/path/to'); // -> false
Link::isMailHref('https://example.com'); // -> false

Link::isMailHref('mailto:example@example.com'); // -> true

// 電話っぽいやつ
Link::isMailHref('tel:+1234567890'); // -> false
```

#### isTelHref
**電話リンクかどうかを判定する**
tel: で始まるURLは電話リンクとみなす。

```php
Link::isTelHref('/path/to'); // -> false
Link::isTelHref('https://example.com'); // -> false

Link::isTelHref('tel:+1234567890'); // -> true

// メール
Link::isTelHref('mailto:example@example.com'); // -> false
```

#### guessBaseUrl
**現在のベースURLを推測する**
`$_SERVER` の値を結合するだけ。
よって、CLIなどでは正常に動作しない。

```php
Link::guessBaseUrl(); // -> http://localhost
```

## Test
```bash
composer run test
```

## CLI 実行
```bash
php psysh.php
```
