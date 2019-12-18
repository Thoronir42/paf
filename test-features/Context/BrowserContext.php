<?php declare(strict_types=1);

namespace Behat\PAF\Context;

use Behat\Behat\Context\Context;
use Behat\Mink\Mink;
use Behat\MinkExtension\Context\MinkAwareContext;

class BrowserContext implements Context, MinkAwareContext
{

    /** @var Mink */
    private $mink;

    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    public function setMinkParameters(array $parameters)
    {
        // TODO: Implement setMinkParameters() method.
    }

    /**
     * @Given I am new visitor
     */
    public function clearSession()
    {
        $this->mink->resetSessions();
    }
}
