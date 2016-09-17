<?php

namespace App\Controls\Views;


use App\Model\Entity\Quote;

class QuoteView extends BaseView
{
    public function renderOverview(Quote $quote)
    {
        $this->template->quote = $quote;

        $this->template->setFile(__DIR__ . '/quoteOverview.latte');

        $this->template->render();
    }
}
