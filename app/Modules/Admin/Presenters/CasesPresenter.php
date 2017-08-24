<?php

namespace App\Modules\Admin\Presenters;


use App\Common\Controls\Views\QuoteView\QuoteView;
use App\Common\Services\Doctrine\Quotes;
use Nette\Application\UI\Multiplier;

class CasesPresenter extends AdminPresenter
{
    /** @var Quotes @inject */
    public $quotes;

    public function actionList()
    {
        $this->template->quotes = $this->quotes->findForOverview();
    }

    public function createComponentQuote()
    {
        return new Multiplier([$this, 'createQuoteView']);
    }

    public function createQuoteView($name) {
        return new QuoteView($this->template->quotes[$name]);
    }
}
