<?php


namespace Test\SeStep\LeanSettings;


use SeStep\GeneralSettings\IOptions;
use SeStep\LeanSettings\LeanOptions;
use Test\PAF\Utils\TestUtils;
use Test\SeStep\GeneralSettings\GenericOptionsTest;

class LeanOptionsTest extends GenericOptionsTest
{
    protected function getOptions(): IOptions
    {
        /** @var LeanOptions $instance */
        $instance = TestUtils::getContainer()->getService('leanSettings.options');

        return $instance;
    }
}