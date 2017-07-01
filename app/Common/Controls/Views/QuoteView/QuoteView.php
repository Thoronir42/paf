<?php

namespace App\Common\Controls\Views\QuoteView;


use App\Common\Controls\Views\BaseView;
use App\Common\Model\Entity\Quote;

class QuoteView extends BaseView
{
    public function renderOverview(Quote $quote)
    {
        $this->template->quote = $quote;

        $this->template->setFile(__DIR__ . '/quoteOverview.latte');

        $this->template->render();
    }
}
