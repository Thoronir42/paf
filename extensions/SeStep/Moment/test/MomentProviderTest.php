<?php declare(strict_types=1);

namespace SeStep\Moment;

use PHPUnit\Framework\TestCase;

class MomentProviderTest extends TestCase
{
    public function testGetNow()
    {
        $provider = new RelativeMomentProvider(new \DateTime("2012-12-21 4:20"));

        $this->assertEquals(new \DateTime("2012-12-21 4:20"), $provider->now());
    }

    public function testGetNowWithoutModification()
    {
        $provider = new RelativeMomentProvider(new \DateTime("2012-12-21 4:20"));

        $date = $provider->now();

        $date->setTime(3, 15);

        $this->assertEquals(new \DateTime("2012-12-21 4:20"), $provider->now());
    }
}
