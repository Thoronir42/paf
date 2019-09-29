<?php declare(strict_types=1);

namespace SeStep\EntityIds;

use PHPUnit\Framework\TestCase;
use SeStep\EntityIds\Type\CheckSum;

class EncodedTypeIdGeneratorTest extends TestCase
{
    /**
     *
     *
     * @param string $expectedExceptionMessage
     * @param int $length
     * @param array $typeMap
     *
     * @dataProvider invalidArgumentsData
     */
    public function testInvalidArguments(
        string $expectedExceptionMessage,
        int $length,
        array $typeMap = []
    ) {
        $this->expectExceptionMessage($expectedExceptionMessage);

        $charSet = new CharSet('ABCDabcd');
        new EncodedTypeIdGenerator($charSet, new CheckSum($charSet), $typeMap, $length);
    }

    public function invalidArgumentsData()
    {
        return [
            'invalid check sum type' => [
                "CheckSum 'hello' is not an integer",
                6,
                [
                    'hello' => 'world',
                ],
            ],
            'invalid check sum value < 0' => [
                "Checksum for type 'apple' (-1) is not within allowed range <0, 8)",
                6,
                [
                    -1 => 'apple',
                ],
            ],
            'invalid check sum value > $maxChecksum' => [
                "Checksum for type 'apple' (8) is not within allowed range <0, 8)",
                6,
                [
                    8 => 'apple',
                ],
            ],
        ];
    }

    public function testGetId()
    {
        $charSet = new CharSet('ABC');
        $generator = new EncodedTypeIdGenerator($charSet, new CheckSum($charSet), [], 6);

        $this->assertRegExp('/[ABC]{6}/', $generator->generateId());
    }

    public function testGetIdCheckType()
    {
        $types = [
            2 => 'potion',
            7 => 'key',
            11 => 'door',
        ];

        $charSet = new CharSet('0123');
        $generator = new EncodedTypeIdGenerator($charSet, new CheckSum($charSet, [1, 3, 7]), $types, 10);

        $ids = array_map(function ($type) use ($generator) {
            return $generator->generateId($type);
        }, $types);

        foreach ($ids as $id) {
            $this->assertRegExp('/[0123]{10}/', $id);
        }

        $recognizedTypes = array_map(function ($id) use ($generator) {
            return $generator->getType($id);
        }, $ids);

        $this->assertEquals($types, $recognizedTypes);
    }
}
