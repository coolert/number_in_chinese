<h1> number_in_chinese </h1>

<p>将数字转换为中文汉字。支持整数与浮点数，简繁体输出，目前不支持负数。</p>


## 安装

```shell
$ composer require coolert/number_in_chinese -vvv
```

## 方法

* convertNumbers(string $number,int dic,int unit_dic)  
**说明**：数字转换成中文  
**参数**：`$number`为需要转换的数字。`$dic`转换成的中文数字类型，1 小写(数字'0'用'零’表示) 2 小写('0'用'〇‘表示) 3 大写中文('壹','贰','叁'...)。
`$unit_dic`转换后的单位类型，1 简体 2 繁体。

## 使用

```php
use Coolert\NumberInChinese\Convert;
$convert = new Convert();
//转换为小写简体中文，输出 '一亿二千三百四十五万七千八百九十'
$response = $convert->convertNumbers('1234567890', 1, 1);
echo $response;
//转换为大写数字繁体中文，输出 '拾贰億叁千肆百伍拾陆萬柒千捌百镹拾'
$response = $convert->convertNumbers('1234567890', 3, 2);
echo $response;
```

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