<?php declare(strict_types=1);

namespace Test\SeStep\GeneralSettingsInMemory;

use SeStep\GeneralSettings\IValuePoolsAdapter;
use SeStep\GeneralSettingsInMemory\InMemoryOptionsAdapter;
use Test\SeStep\GeneralSettings\GenericValuePoolTest;

class InMemoryValuePoolTest extends GenericValuePoolTest
{

    /** @return IValuePoolsAdapter */
    protected function getPoolAdapter()
    {
        return new InMemoryOptionsAdapter();
    }
}
