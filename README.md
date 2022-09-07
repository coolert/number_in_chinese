<h1 align="center"> number_in_chinese </h1>

<p align="center">将数字转换为中文汉字的简单php组件。支持整数与浮点数，简繁体输出，目前不支持负数。</p>

[![Tests](https://github.com/coolert/number_in_chinese/actions/workflows/tests.yml/badge.svg)](https://github.com/coolert/number_in_chinese/actions/workflows/tests.yml)
![StyleCI build status](https://github.styleci.io/repos/517887313/shield)
## 环境需求

- PHP >= 7.3

## 安装

```shell
$ composer require coolert/number_in_chinese -vvv
```

## 使用

```php
use Coolert\NumberInChinese\Convert;
$convert = new Convert();
echo $response;
```

## 数字转换成中文

```php
$response = $convert->convertNumbers('1234567890');
```

示例：

```php
一亿二千三百四十五万七千八百九十
```

## 参数说明

```php
string convertNumbers(string $number, int $dic = 1, int $unit_dic = 1)
```

- $number - 需要转换的数字
- $dic - 转换成的数字位类型：1：小写，数字'0'用'零’表示 2：小写，数字'0'用'〇‘表示 3：大写('壹','贰','叁'...)
- $unit_dic - 转换后的单位类型：1：简体中文 2：繁体中文

## 在Laravel中使用

你可以有两种方式获取`Coolert\NumberInChinese\Convert`实例:

### 方法参数注入

```php
public function index(Convert $convert)
{
    $response = $convert->convertNumbers('123457890');
}
```

### 服务名访问

```php
public fuction index()
{
    $response = app('convert')->convertNumbers('123457890');
}
```

## License

MIT