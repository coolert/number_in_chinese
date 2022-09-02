<?php

/*
 * This file is part of the coolert/number_in_chinese.
 *
 * (c) coolert <keith920627@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Coolert\NumberInChinese\Tests;

use Coolert\NumberInChinese\Convert;
use Coolert\NumberInChinese\Exceptions\DictionarySetException;
use Coolert\NumberInChinese\Exceptions\ExtensionException;
use Coolert\NumberInChinese\Exceptions\InvalidArgumentException;
use Coolert\NumberInChinese\Exceptions\TypeSetException;
use PHPUnit\Framework\TestCase;

class ConvertTest extends TestCase
{
    public function testBcmathException()
    {
        $convert = new Convert();
        $this->expectException(ExtensionException::class);
        $this->expectExceptionMessage('Disabled extension bcmath');
        $extensions = [
            'bcmath' => false,
            'mbstring' => true,
        ];
        $convert->extensionException($extensions);
    }

    public function testMbstringException()
    {
        $convert = new Convert();
        $this->expectException(ExtensionException::class);
        $this->expectExceptionMessage('Disabled extension mbstring');
        $extensions = [
            'bcmath' => true,
            'mbstring' => false,
        ];
        $convert->extensionException($extensions);
    }

    public function testFormatNumber()
    {
        $convert = new Convert();
        $this->assertSame('12340', $convert->formatNumber('\r\x0B 000  123 40 \t\n  '));
        $this->assertSame('0.04', $convert->formatNumber('00.040'));
        $this->assertSame('12', $convert->formatNumber('12.00'));
        $this->assertSame('0', $convert->formatNumber('00.00'));
        $convert->formatNumber('10');
        $this->assertSame('int', $convert->type);
        $convert->formatNumber('1.3');
        $this->assertSame('float', $convert->type);
    }

    public function testFormatNumberWithInvalidNumberType()
    {
        $convert = new Convert();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid number type, must be a string');
        $convert->formatNumber(12);
        $this->fail('Failed to assert formatNumber throw exception with invalid number type.');
    }

    public function testFormatNumberWithInvalidNumber()
    {
        $convert = new Convert();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value number: 12b');
        $convert->formatNumber('12b');
        $this->fail('Failed to assert formatNumber throw exception with invalid number value.');
    }

    public function testSelectDictionariesWithInvalidDictionaryType()
    {
        $convert = new Convert();
        $this->expectException(DictionarySetException::class);
        $this->expectExceptionMessage('Invalid dictionary type');
        $convert->selectDictionaries(4, 1);
        $this->fail('Failed to assert selectDictionaries throw exception with invalid dictionary type.');
    }

    public function testSelectDictionariesWithInvalidUnitDictionaryType()
    {
        $convert = new Convert();
        $this->expectException(DictionarySetException::class);
        $this->expectExceptionMessage('Invalid unit dictionary type');
        $convert->selectDictionaries(1, 3);
        $this->fail('Failed to assert selectDictionaries throw exception with invalid unit dictionary type.');
    }

    public function testSelectDictionaries()
    {
        $convert = new Convert();
        $convert->selectDictionaries(1, 1);
        $this->assertSame($convert->dic, $convert::SIMPLE_DIC);
        $this->assertSame($convert->unit_dic, $convert::SIMPLE_UNIT_DIC);
        $convert->selectDictionaries(2, 2);
        $this->assertSame($convert->dic, $convert::SIMPLE_SPEC_DIC);
        $this->assertSame($convert->unit_dic, $convert::TRADITION_UNIT_DIC);
        $convert->selectDictionaries(3, 2);
        $this->assertSame($convert->dic, $convert::UPPER_DIC);
    }

    public function testConvertIntegerWithNoDictionary()
    {
        $convert = new Convert();
        $this->expectException(DictionarySetException::class);
        $this->expectExceptionMessage('Dictionary is not set');
        $convert->convertInteger('100');
        $this->fail('Failed to assert convertInteger throw exception with dictionary not set');
    }

    public function testConvertIntegerWithNoUnitDictionary()
    {
        $convert = new Convert();
        $this->expectException(DictionarySetException::class);
        $convert->dic = $convert::SIMPLE_DIC;
        $this->expectExceptionMessage('Unit dictionary is not set');
        $convert->convertInteger('100');
        $this->fail('Failed to assert convertInteger throw exception with unit dictionary not set');
    }

    public function testConvertInteger()
    {
        $convert = new Convert();
        $convert->dic = $convert::SIMPLE_DIC;
        $convert->unit_dic = $convert::SIMPLE_UNIT_DIC;
        $this->assertSame('零', $convert->convertInteger('0'));
        $this->assertSame('一百', $convert->convertInteger('100'));
        $this->assertSame('一亿零二千零一', $convert->convertInteger('100002001'));
        $this->assertSame('一兆二千三百亿零四百五十六万七千八百九十', $convert->convertInteger('1230004567890'));
        $convert->dic = $convert::SIMPLE_SPEC_DIC;
        $this->assertSame('一兆二千三百亿〇四百五十六万七千八百九十', $convert->convertInteger('1230004567890'));
        $convert->dic = $convert::UPPER_DIC;
        $convert->unit_dic = $convert::TRADITION_UNIT_DIC;
        $this->assertSame('壹兆贰千叁百億零肆百伍拾陆萬柒千捌百镹拾', $convert->convertInteger('1230004567890'));
    }

    public function testConvertDecimalWithNoDictionary()
    {
        $convert = new Convert();
        $this->expectException(DictionarySetException::class);
        $this->expectExceptionMessage('Dictionary is not set');
        $convert->convertDecimal('100');
        $this->fail('Failed to assert convertDecimal throw exception with dictionary not set');
    }

    public function testConvertDecimal()
    {
        $convert = new Convert();
        $convert->dic = $convert::SIMPLE_DIC;
        $this->assertSame('零一二三四五六七八九', $convert->convertDecimal('0123456789'));
        $convert->dic = $convert::SIMPLE_SPEC_DIC;
        $this->assertSame('〇一二三四五六七八九', $convert->convertDecimal('0123456789'));
        $convert->dic = $convert::UPPER_DIC;
        $this->assertSame('零壹贰叁肆伍陆柒捌镹', $convert->convertDecimal('0123456789'));
    }

    public function testConvertNumbersWithInvalidType()
    {
        $this->expectException(TypeSetException::class);
        $this->expectExceptionMessage('Invalid type set');
        $convert = \Mockery::mock(Convert::class)->makePartial();
        $convert->allows()->formatNumber('123')->andReturn('123');
        $convert->convertNumbers('123');
        $this->fail('Failed to assert convertNumbers throw exception with Invalid type set');
    }

    public function testConvertNumbers()
    {
        $convert = new Convert();
        $this->assertSame('十二亿三千四百五十六万七千八百九十', $convert->convertNumbers('1234567890'));
        $this->assertSame('十二亿三千四百五十六万七千八百九十点八七五', $convert->convertNumbers('1234567890.875'));
        $this->assertSame('十二亿三千四百五十六万七千八百九十', $convert->convertNumbers('0001234567890.000'));
    }
}
