<?php declare(strict_types=1);

namespace Behat\PAF\Modules\CommissionModule;


use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\PAF\Utils\MinkContext;

class QuoteListContext implements Context, MinkAwareContext
{
    use MinkContext;

    /**
     * @Then /^I (accept|reject) quote "([^"]+)"$/
     */
    public function determineQuote($resolution, string $quoteCaption)
    {
        $quotes = $this->getPage()->findAll('css', '.quote-overview');

        $quote = array_filter($quotes, function (NodeElement $element) use ($quoteCaption) {
            $title = $element->find('xpath', "//*[text()='$quoteCaption']");
            return !!$title;
        });

        if (empty($quote)) {
            throw new ElementNotFoundException($this->getMinkSession()->getDriver(), 'quote card',
                'css', '.quote-overview');
        }
        if (count($quote) > 1) {
            throw new ElementNotFoundException($this->getMinkSession()->getDriver(), 'quote card ambiguous',
                'css', '.quote-overview');
        }
        $quoteEl = current($quote);


        $link = $quoteEl->find('xpath', "//a[contains(@href, '$resolution')]");
        if ($link instanceof NodeElement) {
            $this->visit($link->getAttribute('href'));
        } else {
            throw new ElementNotFoundException($this->getMinkSession()->getDriver(), "$resolution link",
                'xpath', "//a[contains(@href, '$resolution')]");
        }
    }
}
