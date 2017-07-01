<?php

namespace App\Modules\Front\Presenters;


use App\Common\Controls\Forms\QuoteForm\IQuoteFormFactory;

class QuotesPresenter extends FrontPresenter
{
    /** @var IQuoteFormFactory @inject */
    public $form_factory;

    public function startup()
    {
        parent::startup();
    }

    public function actionDefault()
    {
        $this->template->enableQuotes = $this->settings->getValue('paf.quotes.enable_quotes');
    }

    public function createComponentQuoteForm()
    {
        $form = $this->form_factory->create();

        return $form;
    }
}
