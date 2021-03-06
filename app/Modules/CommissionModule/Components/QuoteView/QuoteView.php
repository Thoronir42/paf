<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\QuoteView;

use Nette\Application\UI\Control;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommonModule\Components\PhotoBox\PhotoBoxControl;

/**
 * Class QuoteView
 * @package PAF\Common\Controls\Views\QuoteView
 *
 * @method onAccept(Quote $quote)
 * @method onReject(Quote $quote)
 */
class QuoteView extends Control
{
    public $onAccept = [];

    public $onReject = [];

    /** @var Quote */
    private $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    public function renderTile()
    {
        $this->template->quote = $this->quote;

        $this->template->setFile(__DIR__ . '/quoteTile.latte');

        $this->template->render();
    }

    public function handleAccept()
    {
        $this->onAccept($this->quote);
    }

    public function handleReject()
    {
        $this->onReject($this->quote);
    }

    public function createComponentReferences()
    {
        return new PhotoBoxControl($this->quote->specification->references);
    }
}
