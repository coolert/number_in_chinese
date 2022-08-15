<?php

namespace Coolert\NumberInChinese;

use Coolert\NumberInChinese\Exceptions\DictionarySetException;
use Coolert\NumberInChinese\Exceptions\ExtensionException;
use Coolert\NumberInChinese\Exceptions\InvalidArgumentException;
use Coolert\NumberInChinese\Exceptions\TypeSetException;

/**
 * Class Convert
 */
class Convert
{
    const SIMPLE_DIC = ['零','一', '二', '三', '四', '五', '六', '七', '八', '九', '十',];
    const SIMPLE_SPEC_DIC = ['〇','一', '二', '三', '四', '五', '六', '七', '八', '九', '十',];
    const UPPER_DIC = ['零','壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '镹', '拾',];
    const SIMPLE_UNIT_DIC = ['无量大数','万','亿','兆','京','垓','秭','穰','沟','涧','正','载','极','恒河沙','阿僧祇','那由他','不可思议'];
    const TRADITION_UNIT_DIC = ['無量大數','萬','億','兆','京','垓','秭','穰','溝','澗','正','載','極','恆河沙','阿僧祇','那由他','不可思議'];
    const EXTENSION = ['bcmath','mbstring'];

    public $dic;
    public $unit_dic;
    public $type;

    /**
     * @throws ExtensionException
     */
    public function __construct()
    {
        $extensions = [];
        foreach (self::EXTENSION as $name) {
            $extensions[$name] = \extension_loaded($name) === true;
        }
        $this->extensionException($extensions);
    }

    /**
     * Check php extension.
     *
     * @param array $extensions
     *
     * @throws ExtensionException
     */
    public function extensionException($extensions)
    {
        foreach ($extensions as $name => $state) {
            if ($state === false) {
                throw new ExtensionException('Disabled extension ' . $name);
            }
        }
    }

    /**
     * Convert numbers into Chinese numbers.
     *
     * @param int $number
     * @param int $character
     * @param int $unit
     *
     * @return string
     *
     * @throws InvalidArgumentException
     * @throws DictionarySetException
     * @throws TypeSetException
     */
    public function convertNumbers($number,$character = 1,$unit = 1)
    {
        $number = $this->formatNumber($number);
        $this->selectDictionaries($character, $unit);
        if ($this->type == 'float') {
            $number_arr = \explode('.', $number);
            $integer_part = $this->convertInteger($number_arr[0]);
            $decimal_part = $this->convertDecimal($number_arr[1]);
            return $integer_part . '点' . $decimal_part;
        } elseif ($this->type == 'int') {
            return $this->convertInteger($number);
        } else {
            throw new TypeSetException('Invalid type set');
        }
    }

    /**
     * Replace Chinese characters string.
     *
     * @param string $string
     * @param string $replacement
     * @param int $start
     * @param int $length
     * @param string $encoding
     *
     * @return string
     */
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

    /**
     * Select character dictionary and unit dictionary.
     *
     * @param int $character
     * @param int $unit
     *
     * @throws DictionarySetException
     */
    public function selectDictionaries($character, $unit)
    {
        switch ($character) {
            case 1:
                $dic = self::SIMPLE_DIC;
                break;
            case 2:
                $dic = self::SIMPLE_SPEC_DIC;
                break;
            case 3:
                $dic = self::UPPER_DIC;
                break;
            default:
                throw new DictionarySetException('Invalid dictionary type');
        }
        $this->dic = $dic;
        switch ($unit) {
            case 1:
                $unit_dic = self::SIMPLE_UNIT_DIC;
                break;
            case 2:
                $unit_dic = self::TRADITION_UNIT_DIC;
                break;
            default:
                throw new DictionarySetException('Invalid unit dictionary type');
        }
        $this->unit_dic = $unit_dic;
    }

    /**
     * Check input data and format data into usable numbers.
     *
     * @param $number
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function formatNumber($number)
    {
        if (!\is_string($number)) {
            throw new InvalidArgumentException('Invalid number type, must be a string');
        }
        $number = \ltrim(\trim(\str_replace(' ','', $number), ' \t\n\r'),'\0\x0B');
        $pos_dot = \strpos($number,'.');
        if ($pos_dot !== false) {
            if ($pos_dot === 0) {
                $number = '0' . $number;
            }
            $number = \rtrim(\rtrim($number, '0'), '.');
        }
        if (\preg_match('/^\d+(\.{0,1}\d+){0,1}$/', $number) === 0) {
            throw new InvalidArgumentException('Invalid value number: ' . $number);
        }
        if (\strpos($number, '.') === false) {
            $this->type = 'int';
        } else {
            $this->type = 'float';
        }
        return $number;
    }

    /**
     * Convert integer part.
     *
     * @param $integer
     *
     * @return string
     *
     * @throws DictionarySetException
     */
    public function convertInteger($integer)
    {
        if (empty($this->dic)) {
            throw new DictionarySetException('Dictionary is not set');
        }
        if (empty($this->unit_dic)) {
            throw new DictionarySetException('Unit dictionary is not set');
        }
        $num_arr_chunk =  \array_chunk(\array_reverse(\str_split($integer)),68);
        $complete_str = '';
        foreach ($num_arr_chunk as $cycle => $num_arr){
            $length = \count($num_arr);
            $match_unit_digits = $length%4 == 0 ? \bcdiv($length, 4) : \bcdiv($length, 4)+1;
            $chunk_unit_dic = \array_slice($this->unit_dic, 0, $match_unit_digits);
            if ($cycle == 0) {
                $chunk_unit_dic[0] = '';
            }
            for ($a = 0; $a < $match_unit_digits; $a++) {
                if ($this->dic == $this::UPPER_DIC) {
                    \array_splice($chunk_unit_dic, $a*4+1, 0, ['拾', '百', '千',]);
                } else {
                    \array_splice($chunk_unit_dic, $a*4+1, 0, ['十', '百', '千',]);
                }
            }
            $chinese_num = '';
            foreach ($num_arr as $key => $value) {
                if ($key % 4 == 0) {
                    if ($key > 4 && $chunk_unit_dic[$key-4] == \mb_substr($chinese_num,0,1)){
                        $chinese_num = $this->mbSubStrReplace($chinese_num,'',0,1);
                        if (\mb_substr($chinese_num, 0, 1) != $this->dic[0]) {
                            $chinese_num = $this->dic[0].$chinese_num;
                        }
                    }
                    $chinese_num = ($value == 0 && $length != 1 ? '' : $this->dic[$value]).$chunk_unit_dic[$key].$chinese_num;
                } else {
                    if ($value == 0 && $chinese_num == ''){
                        $chinese_num = ''.$chinese_num;
                    }elseif ($value == 0 && $chinese_num != ''){
                        if ($num_arr[$key-1] != 0){
                            $chinese_num = $this->dic[0].$chinese_num;
                        }
                    }else{
                        if ($value == 1 && $length%4 == 2 && $length == $key+1){
                            $chinese_num = $chunk_unit_dic[$key].$chinese_num;
                        }else{
                            $chinese_num = $this->dic[$value].$chunk_unit_dic[$key].$chinese_num;
                        }
                    }
                }
            }
            $complete_str = $chinese_num.$complete_str;
        }
        return $complete_str;
    }

    /**
     * Convert decimal part.
     *
     * @param $decimal
     *
     * @return string
     *
     * @throws DictionarySetException
     */
    public function convertDecimal($decimal)
    {
        if (empty($this->dic)) {
            throw new DictionarySetException('Dictionary is not set');
        }
        $num_arr = \str_split($decimal);
        $converted_str = '';
        foreach ($num_arr as $v) {
            $converted_str .= $this->dic[$v];
        }
        return $converted_str;
    }
}