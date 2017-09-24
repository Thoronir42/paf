<?php

namespace App\Common\Controls\Forms\QuoteForm;


use App\Common\Controls\Forms\BaseFormControl;
use App\Common\Helpers\LocalizationHelper;
use App\Common\Model\Embeddable\Contact;
use App\Common\Model\Embeddable\FursuitSpecification;
use App\Common\Model\Entity\Fursuit;
use App\Common\Model\Entity\Quote;
use App\Common\Services\Doctrine\Quotes;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

/**
 * Class QuoteForm
 *
 * @method onSave(Quote $quote, Form $form, ArrayHash $values)
 */
class QuoteForm extends BaseFormControl
{
    /** @var callable[]  function (Form $form, ArrayHash $values); Occurs when form successfully validates input. */
    public $onSave;

    /** @var Quotes */
    private $quote;


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
        $contactTranslator = $this->translator->domain('paf.contact');
        $contact = $form->addContainer('contact');
        $contact->addText('name', 'name')->setTranslator($contactTranslator)
            ->setRequired(true)
            ->addRule(Form::MIN_LENGTH, 'name-min-length', 3);
        $contact->addText('email', 'email')->setTranslator($contactTranslator);
        $contact->addText('telegram', 'telegram')->setTranslator($contactTranslator);

        $form->addGroup('group-fursuit');
        $fursuitContainer = $form->addContainer('fursuit');

        $fursuitContainer->addText('name', 'fursuit-name');
        $fursuitContainer->addTextArea('characterDescription', 'character-description')
            ->setOption('help-text', 'character-description-help');
        $fursuitContainer->addSelect('type', 'paf.fursuit.type', LocalizationHelper::getFursuitTypes())
            ->setAttribute('data-minimum-results-for-search', 'Infinity')
            ->setTranslator($this->translator);


        $additionals = $form->addContainer('additionals');
        $additionals->addCheckbox('sleeves', 'long-sleeves');

        $form->addGroup('group-additional-info');

        $form->addMultiUpload('reference', 'references');


        $form->addGroup('group-finish');
        $form->addSubmit('save', 'submit')
            ->setTranslator($this->translator->domain('generic'));


        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }

    public function processForm(Form $form, $values)
    {
        $contact = (new Contact($values->contact->name))
            ->setEmail($values->contact->email)
            ->setTelegram($values->contact->telegram);

        $fursuitSpecification = (new FursuitSpecification($values->fursuit->name))
            ->setType($values->fursuit->type)
            ->setCharacterDescription($values->fursuit->characterDescription);

        $quote = $this->quote ?: new Quote($contact, $fursuitSpecification);

        $this->onSave($quote, $form, $values->reference);
    }
}

interface IQuoteFormFactory
{
    /** @return QuoteForm */
    public function create();
}
