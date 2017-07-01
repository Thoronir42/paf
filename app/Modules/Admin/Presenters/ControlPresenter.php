<?php

namespace App\Modules\Admin\Presenters;


use App\Common\Services\Doctrine\Quotes;

class ControlPresenter extends AdminPresenter
{
    /** @var Quotes @inject */
    public $quotes;

    public function startup()
    {
        parent::startup();
    }

    public function actionDefault()
    {

    }

    public function actionQuotes()
    {
        $this->template->quotes = $this->quotes->findForOverview();
    }

    public function handleSwitchQuotes($enable)
    {

    }

    public function createComponentQuote()
    {
        return new QuoteView();
    }


}
