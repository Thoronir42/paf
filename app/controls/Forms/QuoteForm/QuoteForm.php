<?php

namespace App\Forms;


use App\Forms\BaseFormControl;
use App\Forms\FormFactory;
use App\Model\Entity\Fursuit;
use App\Model\Entity\Quote;
use App\Model\Services\Quotes;
use Nette\Application\UI\Form;

class QuoteForm extends BaseFormControl
{
	/** @var Quotes */
	private $quote;


	public function __construct(FormFactory $factory)
	{
		parent::__construct($factory);

		$this->quote = new Quote();
	}

	public function setEntity(Quote $quote)
	{
		$this->quote = $quote;

		$defaults = $quote->toArray();

		$form = $this->form();
		$form->setDefaults($defaults);
	}

	public function render()
	{
		$this->template->setFile(__DIR__ . '/quoteForm.latte');

		$this->template->render();
	}


	public function createComponentForm()
	{
		$form = $this->factory->create();


		$form->addGroup('Kontaktní údaje');
		$form->addText('name', 'Jméno :3333');

		$form->addGroup('Typ produktu');
		$form->addSelect('fursuit_type', 'Pokrytí', Fursuit::getTypes());
		$additionals = $form->addContainer('additionals');
		$additionals->addCheckbox('sleeves', 'Dlouhé rukávy');

		$form->addTextArea('misc', 'Různé (křídla, šupiny, ...)');

		$form->addGroup('Doplňující informace');
		$form->addText('height', 'Výška')
			->getControlPrototype()->type = 'number';
		$form->addText('teeth', 'Počet zubů')
			->getControlPrototype()->type = 'number';

		$form->addUpload('refference', 'Ref-sheet');


		$form->addGroup('Potvrdit žádost');
		$form->addSubmit('save', 'Yay!');


		$form->onSuccess[] = $this->processForm;

		return $form;
	}

	public function processForm(Form $form, $values)
	{

	}
}

interface IQuoteFormFactory
{
	/** @return QuoteForm */
	public function create();
}
