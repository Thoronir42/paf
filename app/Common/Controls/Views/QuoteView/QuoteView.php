<?php

namespace App\Common\Controls\Views\QuoteView;


use App\Common\Controls\Views\BaseView;
use App\Common\Model\Entity\Quote;

/**
 * Class QuoteView
 * @package App\Common\Controls\Views\QuoteView
 *
 * @method onAccept(Quote $quote)
 * @method onReject(Quote $quote)
 */
class QuoteView extends BaseView
{
    public $onAccept = [];

    public $onReject = [];

    /** @var Quote */
    private $quote;

    public function __construct(Quote $quote)
    {
        parent::__construct();

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
}
