<?php declare(strict_types=1);

namespace Test\SeStep\GeneralSettings;

use PHPUnit\Framework\TestCase;
use SeStep\GeneralSettings\IValuePoolsAdapter;

abstract class GenericValuePoolTest extends TestCase
{
    /** @return IValuePoolsAdapter */
    abstract protected function getPoolAdapter();

    public function testCreatePool()
    {
        $pools = $this->getPoolAdapter();

        $channels = [
            'r' => 'red',
            'g' => 'green',
            'b' => 'yellow',
            'a' => 'alpha'
        ];

        $pool = $pools->createPool('colorChannels', $channels);
        $this->assertEquals($channels, $pool->getValues());

        $pool = $pools->getPool('colorChannels');
        $this->assertEquals($channels, $pool->getValues());
    }

    public function testUpdateValues()
    {
        $pools = $this->getPoolAdapter();

        $pool = $pools->createPool('beats', [
            '4/4',
            '2/5',
            '4/8',
        ]);
        $this->assertEquals(['4/4', '2/5', '4/8'], $pool->getValues());

        $pools->updateValues($pool, ['4/4', '4/8']);
        $this->assertEquals(['4/4', '4/8'], $pool->getValues());
    }

    public function testDeletePool()
    {
        $pools = $this->getPoolAdapter();

        $pool = $pools->createPool('bigMath', [1, 2, 3]);
        $this->assertNotNull($pool);
        $this->assertCount(3, $pool);

        $pools->deletePool('bigMath');
        $this->assertNull($pools->getPool('bigMath'));
    }
}
