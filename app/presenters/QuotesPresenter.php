<?php

namespace App\Presenters;


use App\Controls\DebtView;
use App\Forms\IDebtFormFactory;
use App\Forms\IQuoteFormFactory;
use App\Model\Entity\Quote;

class QuotesPresenter extends BasePresenter
{
    /** @var IQuoteFormFactory @inject */
    public $form_factory;

    public function startup()
    {
        parent::startup();
    }

    public function actionDefault()
    {
        $this->template->enableQuotes = $this->settings->getValue('enable_quotes');
    }

    public function createComponentQuoteForm()
    {
        $form = $this->form_factory->create();

        return $form;
    }
}
