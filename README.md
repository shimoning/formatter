# formatter
各種変換ユーティリティ

## Install

利用するプロジェクトの `composer.json` に以下を追加する。

### git コマンドを使う方法
```cli
composer config repositories.shimoning/formatter vcs https://github.com/shimoning/formatter.git
```

### 手動で composer.json を編集する方法
```composer.json
"repositories": {
    "formatter": {
        "type": "vcs",
        "url": "https://github.com/shimoning/formatter.git"
    }
},
```

その後以下でインストールする。

```bash
composer require shimoning/formatter
```

## Support

PHP 7.3 以上

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

第1引数は、1桁目の数字。
第2引数は、桁数。

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


## Test
```bash
composer run test
```

## CLI 実行
```bash
php psysh.php
```
