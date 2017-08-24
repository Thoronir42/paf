<?php

namespace App\Common\Controls\Views\QuoteView;


use App\Common\Controls\Views\BaseView;
use App\Common\Model\Entity\Quote;

class QuoteView extends BaseView
{
    /** @var Quote */
    private $quote;

    public function __construct(Quote $quote)
    {
        parent::__construct();

        $this->quote = $quote;
    }

    public function renderOverview()
    {
        $this->template->quote = $this->quote;

        $this->template->setFile(__DIR__ . '/quoteOverview.latte');

        $this->template->render();
    }
}
