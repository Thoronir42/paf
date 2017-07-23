<?php

namespace App\Common\Controls\Forms\QuoteForm;


use App\Common\Controls\Forms\BaseFormControl;
use App\Common\Controls\Forms\FormFactory;
use App\Common\Model\Entity\Fursuit;
use App\Common\Model\Entity\Quote;
use App\Common\Services\Doctrine\Quotes;
use Kdyby\Translation\Translator;
use Nette\Application\UI\Form;

/**
 * Class QuoteForm
 *
 * @method onSave(Quote $quote, Form $form)
 */
class QuoteForm extends BaseFormControl
{
    /** @var callable[]  function (Form $form, ArrayHash $values); Occurs when form successfully validates input. */
    public $onSave;

    /** @var Quotes */
    private $quote;


    public function __construct(FormFactory $factory, Translator $translator)
    {
        parent::__construct($factory, $translator);
        $this->onSave;
        $this->quote = new Quote("");

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
        $form->setTranslator($this->translator->domain('paf.quote-form'));


        $form->addGroup('group-contact');
        $contact = $form->addContainer('contact');
        $contact->addText('name', 'name');

        $form->addGroup('group-fursuit');
        $fursuitContainer = $form->addContainer('fursuit');

        $fursuitContainer->addText('name', 'fursuit-name');
        $fursuitContainer->addTextArea('character_description', 'character-description');
        $fursuitContainer->addSelect('type', 'type', self::getFursuitTypes())
            ->setTranslator($this->translator->domain('paf.fursuit'));



        $additionals = $form->addContainer('additionals');
        $additionals->addCheckbox('sleeves', 'long-sleeves');

        $form->addGroup('group-additional-info');
        $form->addText('teeth', 'teeth-amount')
            ->getControlPrototype()->type = 'number';

        $form->addMultiUpload('reference', 'references');


        $form->addGroup('group-finish');
        $form->addSubmit('save', 'submit')
            ->setTranslator($this->translator->domain('generic'));


        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }

    public function processForm(Form $form, $values)
    {
        $this->quote->setName($values->contact->name);
        $this->quote->setType($values->fursuit->type);
        $this->quote->setSleeveLength($values->additionals->sleeves ? 80 : 0);

        $this->onSave($this->quote, $form);
    }

    private static function getFursuitTypes()
    {
        $types = [];
        foreach (Fursuit::getTypes() as $type){
            $types[$type] = "types.$type";
        }

        return $types;
    }
}

interface IQuoteFormFactory
{
    /** @return QuoteForm */
    public function create();
}
