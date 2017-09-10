<?php

namespace App\Modules\Admin\Controls\QuotesControl;


use App\Common\Controls\Views\QuoteView\QuoteView;
use App\Common\Model\Entity\Quote;
use App\Common\Services\Doctrine\PafEntities;
use Nette\Application\UI\Control;
use Nette\InvalidStateException;
use SeStep\SettingsInterface\Options\IOptions;

/**
 * Class QuotesControl
 * @package App\Modules\Admin\Controls\QuotesControl
 *
 * @method onAccept(Quote $quote)
 * @method onReject(Quote $quote)
 */
class QuotesControl extends Control
{
    public $onAccept = [];

    public $onReject = [];

    /** @var Quote[] */
    private $quotes = [];

    /** @var PafEntities */
    private $pafEntities;

    /** @var IOptions */
    private $options;

    public function injectPafEntities(PafEntities $pafEntities)
    {
        $this->pafEntities = $pafEntities;
    }

    public function injectOptions(IOptions $options)
    {
        $this->options = $options;
    }

    /** @param Quote[] $quotes */
    public function setQuotes($quotes)
    {
        $this->quotes = $quotes;
    }

    public function renderTiles()
    {
        $template = $this->createTemplate();

        $template->quotes = $this->quotes;
        $template->setFile(__DIR__ . '/quotesControlTiles.latte');

        $template->render();
    }

    public function renderEnableSwitch()
    {
        $template = $this->createTemplate();

        $template->quotesEnabled = $this->options->getValue('paf.quotes.enable_quotes');
        $template->setFile(__DIR__ . '/quotesControlEnableSwitch.latte');

        $template->render();
    }

    public function createComponent($name)
    {
        if (!array_key_exists($name, $this->quotes)) {
            throw new InvalidStateException("Quote $name has not been set");
        }

        $quoteView = new QuoteView($this->quotes[$name]);

        $quoteView->onAccept[] = function (Quote $quote) {
            $this->onAccept($quote);
        };

        $quoteView->onReject[] = function (Quote $quote) {
            $this->onReject($quote);
        };

        return $quoteView;
    }

    public function handleEnable()
    {
        $this->options->setValue(true, 'paf.quotes.enable_quotes');
    }

    public function handleDisable()
    {
        $this->options->setValue(false, 'paf.quotes.enable_quotes');
    }
}
