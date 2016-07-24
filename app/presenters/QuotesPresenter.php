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

		//$this->authenticationCheck('Pro kontrolu dluhů je nezbytné přihlášení.', [])
	}

	public function actionDefault(){
		$this->template->enableQuotes = $this->settings->get('enable-quotes');
	}

	public function createComponentQuoteForm()
	{
		$form = $this->form_factory->create();

		return $form;
	}
}
