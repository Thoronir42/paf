<?php


namespace Test\SeStep\GeneralSettingsInMemory;


use SeStep\GeneralSettings\IOptions;
use SeStep\GeneralSettingsInMemory\InMemoryOptions;
use Test\SeStep\GeneralSettings\GenericOptionsTest;

class InMemoryOptionsTest extends GenericOptionsTest
{

    protected function getOptions(): IOptions
    {
        return new InMemoryOptions();
    }


}