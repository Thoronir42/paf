<?php declare(strict_types=1);

namespace SeStep\EntityIds;

use PHPUnit\Framework\TestCase;

class CharSetTest extends TestCase
{
    /**
     * @param $char
     * @param $expectedValue
     *
     * @dataProvider charToValueData
     */
    public function testCharToValue($char, $expectedValue)
    {
        $charSet = new CharSet();

        $this->assertEquals($expectedValue, $charSet->charToValue($char));
    }

    public function testCharToValueInvalid()
    {
        $charSet = new CharSet('abc');

        $this->assertEquals(-1, $charSet->charToValue('d'));
    }

    /**
     * @param $expectedChar
     * @param $value
     *
     * @dataProvider charToValueData
     */
    public function testValueToChar($expectedChar, $value)
    {
        $charSet = new CharSet();

        $this->assertEquals($expectedChar, $charSet->valueToChar($value));
    }

    public function testValueToCharSpecial()
    {
        $charSet = new CharSet('ABCD');
        $this->assertEquals('D', $charSet->valueToChar(-1));
        $this->assertEquals('C', $charSet->valueToChar(-2));
        $this->assertEquals('A', $charSet->valueToChar(-4));
    }

    public function charToValueData()
    {
        return [
            ['A', 0],
            ['E', 4],
            ['Z', 25],
            ['a', 26],
            ['h', 33],
            ['z', 51],
            ['0', 52],
            ['9', 61],
            ['_', 62],
            ['-', 63],
        ];
    }

    public function testValueToCharInvalid()
    {
        $charSet = new CharSet('abc');

        $this->assertNull($charSet->valueToChar(-5));
        $this->assertNull($charSet->valueToChar(4));
    }
}
