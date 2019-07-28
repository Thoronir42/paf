<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\QuotesControl;

use Nette\ComponentModel\IComponent;
use Nette\Application\UI\Control;
use Nette\InvalidStateException;
use PAF\Modules\CommissionModule\Components\QuoteView\QuoteView;
use PAF\Modules\CommissionModule\Model\Quote;

/**
 * Class QuotesControl
 * @package PAF\Modules\Admin\Controls\QuotesControl
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

    public function createComponent($name): IComponent
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
}
