<?php declare(strict_types=1);

namespace Behat\PAF\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\PAF\Extensions\InjectDependencies;
use PHPUnit\Framework\Assert;
use SeStep\GeneralSettings\Settings;

class SettingsContext implements Context, InjectDependencies
{
    /** @var Settings */
    private $settings;

    public function injectSettings(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @Given Setting :settingFqn is :value
     */
    public function settingIs($settingFqn, $value)
    {
        $this->settings->setValue($settingFqn, $value);
    }

    /**
     * @Then /^Setting "([\w.]*)" should be "(truthy|falsy)"$/
     */
    public function settingShouldBeLike($settingFqn, $expectedValue)
    {
        $value = $this->settings->getValue($settingFqn);
        if ($expectedValue == 'truthy') {
            Assert::assertTrue($value == true); // intentional ==
        } elseif ($expectedValue == 'falsy') {
            Assert::assertTrue($value == false); // intentional ==
        } else {
            throw new PendingException("Unexpected value '$expectedValue'");
        }
    }


}
