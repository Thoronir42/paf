<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\QuoteForm;

use PAF\Common\Forms\FormWrapperControl;
use PAF\Modules\CommonModule\Model\Contact;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use PAF\Modules\CommissionModule\Model\Specification;
use PAF\Modules\PortfolioModule\Localization;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommissionModule\Repository\QuoteRepository;

/**
 * Class QuoteForm
 *
 * @method onSave(Quote $quote, Form $form, ArrayHash $values)
 */
class QuoteForm extends FormWrapperControl
{
    /** @var callable[]  function (Form $form, ArrayHash $values); Occurs when form successfully validates input. */
    public $onSave;

    /** @var QuoteRepository */
    private $quote;


    public function setEntity(Quote $quote)
    {
        $this->quote = $quote;

        $form = $this->form();
        $form->setDefaults($quote->getRowData());
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
        $fursuitContainer->addSelect('type', 'paf.fursuit.type', Localization::getFursuitTypes())
            ->setHtmlAttribute('data-minimum-results-for-search', 'Infinity')
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
        $contact = new Contact();
        $contact->name = $values->contact->name;
        $contact->email = $values->contact->email;
        $contact->telegram = $values->contact->telegram;

        $fursuitSpecification = new Specification();
        $fursuitSpecification->name = $values->fursuit->name;
        $fursuitSpecification->type = $values->fursuit;
        $fursuitSpecification->characterDescription = $values->fursuit->characterDescription;

        $quote = $this->quote ?: new Quote();
        $quote->contact = $contact;
        $quote->specification = $fursuitSpecification;

        $this->onSave($quote, $form, $values->reference);
    }
}