<h1 align="center"> number_in_chinese </h1>

<p align="center">将阿拉伯数字转换为中文汉字 </p>


## 安装

```shell
$ composer require coolert/number_in_chinese -vvv
```

## 使用

```php
use Coolert\NumberInChinese\Convert;
$convert = new Convert();
```

## 将数字转换成中文汉字

```php
$response = $convert->toChineseCharacters(123457890);
```

## 示例

```php
一亿二千三百四十五万七千八百九十
```

## 在Laravel中使用

你可以有两种方式获取`Coolert\NumberInChinese\Convert`实例:

### 方法参数注入

```php
public function index(Convert $convert)
{
    $response = $convert->toChineseCharacters(123457890);
}

```

### 服务名访问

```php
public fuction index()
{
    $response = app('convert')->toChineseCharacters(123457890);
}
```

## License

MIT