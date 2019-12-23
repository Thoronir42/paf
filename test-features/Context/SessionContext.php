<?php declare(strict_types=1);

namespace Behat\PAF\Context;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\PAF\Utils\MinkContext;

class SessionContext implements Context, MinkAwareContext
{
    use MinkContext;

    /**
     * @Given I am new visitor
     */
    public function newVisitor()
    {
        $this->getMinkSession()->reset();
        $this->visit('/');
    }

    /**
     * @Given I sign in as :login
     */
    public function signIn($login)
    {
        [$user, $password] = explode(':', $login, 2) + [null, null];

        $this->visit('/common.sign/in');
        $this->getPage()->fillField('frm-signInForm-form-login', $user);
        $this->getPage()->fillField('frm-signInForm-form-password', $password);
        $this->getPage()->pressButton('send');
    }

    /**
     * @When /^I sign out$/
     */
    public function iSignOut()
    {
        $this->getPage()->clickLink('Sign out');
    }

}
