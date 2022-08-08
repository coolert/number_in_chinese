<?php

namespace Coolert\NumberInChinese\Tests;

use Coolert\NumberInChinese\Convert;
use Coolert\NumberInChinese\Exceptions\ExtensionException;
use Coolert\NumberInChinese\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConvertTest extends TestCase
{
    public function testToChineseCharacters()
    {
        $convert = new Convert();
        $this->assertSame('十二亿三千四百五十六万七千八百九十',$convert->toChineseCharacters('1234567890'));
    }

    public function testMbSubstrReplace()
    {
        $convert = new Convert();
        $this->assertSame('一二三四五六', $convert->mbSubStrReplace('零一二三四五六','', 0, 1));
    }

    public function testToChineseCharactersWithInvalidType()
    {
        $convert = new Convert();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value number: abc');
        $convert->toChineseCharacters('abc');
        $this->fail('Failed to assert toChineseCharacters throw exception with invalid argument.');
    }

    public function testToChineseCharactersWithInvalidNumber()
    {
        $convert = new Convert();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The number must be an int: 11.2');
        $convert->toChineseCharacters('11.2');
        $this->fail('Failed to assert toChineseCharacters throw exception with invalid argument.');
    }

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
}