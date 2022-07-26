<?php

namespace Coolert\NumberInChinese\Tests;

use Coolert\NumberInChinese\Convert;
use Coolert\NumberInChinese\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ConvertTest extends TestCase
{
//    public function testToChineseCharacters()
//    {
//
//    }
//
//    public function testMbSubstrReplace()
//    {
//
//    }

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
}