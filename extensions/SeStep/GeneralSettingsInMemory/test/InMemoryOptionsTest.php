<?php declare(strict_types=1);
namespace Test\SeStep\GeneralSettingsInMemory;

use SeStep\GeneralSettings\IOptionsAdapter;
use SeStep\GeneralSettingsInMemory\InMemoryOptionsAdapter;
use Test\SeStep\GeneralSettings\GenericOptionsTest;

class InMemoryOptionsTest extends GenericOptionsTest
{

    protected function getOptions(): IOptionsAdapter
    {
        return new InMemoryOptionsAdapter();
    }
}
