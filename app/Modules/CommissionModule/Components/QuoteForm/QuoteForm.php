<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\QuoteForm;

use Nette\Http\FileUpload;
use PAF\Common\Forms\Form;
use PAF\Modules\CommissionModule\Model\Specification;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommissionModule\Repository\QuoteRepository;
use PAF\Modules\CommonModule\Model\Contact;

/**
 * Class QuoteForm
 *
 * @method onSave(Quote $quote, Specification $specification, Contact[] $contact, FileUpload[] $references, QuoteForm $form)
 */
class QuoteForm extends Form
{
    /** @var callable[]  function (Form $form, ArrayHash $values); Occurs when form successfully validates input. */
    public $onSave;

    /** @var QuoteRepository */
    private $quote;

    public function __construct()
    {
        parent::__construct();
        $this->quote = new Quote();
    }


    public function setEntity(Quote $quote)
    {
        $this->quote = $quote;

        $this->setDefaults($quote->getRowData());
    }

    public function initialize(array $fursuitTypes): void
    {
        $this->addGroup('paf.quote-form.group-contact');
        $contact = $this->addContainer('contact');

        $contact->addText('email', 'paf.contact.email');
        $contact->addText('telegram', 'paf.contact.telegram');

        $this->addGroup('paf.quote-form.group-fursuit');
        $fursuitContainer = $this->addContainer('fursuit');

        $fursuitContainer->addText('name', 'paf.quote-form.fursuit-name');
        $fursuitContainer->addTextArea('characterDescription', 'paf.quote-form.character-description')
            ->setOption('help-text', 'paf.quote-form.character-description-help');
        $fursuitContainer->addSelect('type', 'paf.fursuit.type', $fursuitTypes)
            ->setHtmlAttribute('data-minimum-results-for-search', 'Infinity');


        $additionals = $this->addContainer('additionals');
        $additionals->addCheckbox('sleeves', 'paf.quote-form.long-sleeves');

        $this->addGroup('paf.quote-form.group-additional-info');

        $this->addMultiUpload('reference', 'paf.quote-form.references');


        $this->addGroup('paf.quote-form.group-finish');
        $this->addSubmit('save', 'submit');

        $this->onValidate[] = [$this, 'validateForm'];

        $this->onSuccess[] = [$this, 'processForm'];
    }

    public function validateForm(QuoteForm $form)
    {
        $values = $form->getValues();
        if (empty($values['contact']['email']) && empty($values['contact']['telegram'])) {
            $form->addError('paf.quote-form.error-no-contact');
        }
    }

    public function processForm(Form $form, $values)
    {

        $fursuitSpecification = new Specification();
        $fursuitSpecification->characterName = $values->fursuit->name;
        $fursuitSpecification->type = $values->fursuit->type;
        $fursuitSpecification->characterDescription = $values->fursuit->characterDescription;

        $this->onSave($this->quote, $fursuitSpecification,
            $this->getContacts($values['contact']), $values->reference, $this);
    }

    private function getContacts($contactSection): array
    {
        $contacts = [];
        foreach ($contactSection as $type => $value) {
            $contact = new Contact();
            $contact->type = $type;
            $contact->value = $value;
            $contacts[$type] = $contact;
        }

        return $contacts;
    }
}
