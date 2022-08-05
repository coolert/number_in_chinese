<?php

namespace Coolert\NumberInChinese;

use Coolert\NumberInChinese\Exceptions\ExtensionException;
use Coolert\NumberInChinese\Exceptions\InvalidArgumentException;

class Convert
{
    public function __construct()
    {
        if ($this->check_bcmath() === false) {
            throw new ExtensionException('Disabled extension bcmath');
        }
        if ($this->check_mbstring() === false) {
            throw new ExtensionException('Disabled extension mbstring');
        }
    }

    public function check_bcmath()
    {
        return \extension_loaded('bcmath');
    }

    public function check_mbstring()
    {
        return \extension_loaded('mbstring');
    }

    public function toChineseCharacters($number)    {
        if (!\is_numeric($number)) {
            throw new InvalidArgumentException('Invalid value number: ' . $number);
        }
        if (!\is_int($number * 1)) {
            throw new InvalidArgumentException('The number must be an int: ' . $number);
        }
        $dic = ['零','一', '二', '三', '四', '五', '六', '七', '八', '九', '十',];
        $unit_dic = ['无量大数','万','亿','兆','京','垓','秭','穰','沟','涧','正','载','极','恒河沙','阿僧祇','那由他','不可思议'];
        $num_array = \array_reverse(\str_split($number));
        $num_arr_chunk =  \array_chunk($num_array,68);
        $complete_str = '';
        foreach ($num_arr_chunk as $cycle => $num_arr){
            $length = \count($num_arr);
            $match_unit_digits = $length%4 == 0 ? \bcdiv($length, 4) : \bcdiv($length, 4)+1;
            $chunk_unit_dic = \array_slice($unit_dic, 0, $match_unit_digits);
            if ($cycle == 0) {
                $chunk_unit_dic[0] = '';
            }
            for ($a = 0; $a < $match_unit_digits; $a++) {
                \array_splice($chunk_unit_dic, $a*4+1, 0, ['十', '百', '千',]);
            }
            $chinese_num = '';
            foreach ($num_arr as $key => $value) {
                if ($key % 4 == 0) {
                    if ($key > 4 && $chunk_unit_dic[$key-4] == \mb_substr($chinese_num,0,1)){
                        $chinese_num = $this->mbSubStrReplace($chinese_num,'',0,1);
                        if (\mb_substr($chinese_num, 0, 1) != $dic[0]) {
                            $chinese_num = $dic[0].$chinese_num;
                        }
                    }
                    $chinese_num = ($value == 0 && $length != 1 ? '' : $dic[$value]).$chunk_unit_dic[$key].$chinese_num;
                } else {
                    if ($value == 0 && $chinese_num == ''){
                        $chinese_num = ''.$chinese_num;
                    }elseif ($value == 0 && $chinese_num != ''){
                        //周期内加'零'
                        if ($num_arr[$key-1] != 0){
                            $chinese_num = $dic[0].$chinese_num;
                        }
                    }else{
                        if ($value == 1 && $length%4 == 2 && $length == $key+1){
                            $chinese_num = $chunk_unit_dic[$key].$chinese_num;
                        }else{
                            $chinese_num = $dic[$value].$chunk_unit_dic[$key].$chinese_num;
                        }
                    }
                }
            }
            $complete_str = $chinese_num.$complete_str;
        }
        return $complete_str;
    }

    public function mbSubStrReplace($string, $replacement, $start, $length = null, $encoding = null)
    {
        $string_length = (\is_null($encoding) === true) ? \mb_strlen($string) : \mb_strlen($string, $encoding);
        if ($start < 0) {
            $start = \max(0, $string_length + $start);
        } elseif ($start > $string_length) {
            $start = $string_length;
        }

        if ($length < 0) {
            $length = \max(0, $string_length - $start + $length);
        } elseif ((\is_null($length) === true) || ($length > $string_length)) {
            $length = $string_length;
        }

        if (($start + $length) > $string_length) {
            $length = $string_length - $start;
        }

        if (\is_null($encoding) === true) {
            return \mb_substr($string, 0, $start) . $replacement . \mb_substr($string, $start + $length, $string_length - $start - $length);
        }

        return \mb_substr($string, 0, $start, $encoding) . $replacement . \mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
    }
}