<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\QuoteForm;

use PAF\Common\Forms\Form;
use PAF\Modules\CommissionModule\Model\Specification;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommissionModule\Repository\QuoteRepository;
use PAF\Modules\CommonModule\Model\Contact;

/**
 * Class QuoteForm
 *
 * @method onSave($quote, $specification, $contact, $references, $form)
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
        $this->addGroup('commission.quote-form.group-contact');
        $contact = $this->addContainer('contact');

        $contact->addText('email', 'paf.contact.email');
        $contact->addText('telegram', 'paf.contact.telegram');

        $this->addGroup('commission.quote-form.group-fursuit');
        $fursuitContainer = $this->addContainer('fursuit');

        $fursuitContainer->addText('name', 'commission.quote-form.fursuit-name');
        $fursuitContainer->addTextArea('characterDescription', 'commission.quote-form.character-description')
            ->setOption('help-text', 'commission.quote-form.character-description-help');
        $fursuitContainer->addSelect('type', 'paf.fursuit.type', $fursuitTypes)
            ->setHtmlAttribute('data-minimum-results-for-search', 'Infinity');


        $additionals = $this->addContainer('additionals');
        $additionals->addCheckbox('sleeves', 'commission.quote-form.long-sleeves');

        $this->addGroup('commission.quote-form.group-additional-info');

        $this->addMultiUpload('reference', 'commission.quote-form.references');


        $this->addGroup();
        $this->addSubmit('save', 'generic.submit');

        $this->onValidate[] = [$this, 'validateForm'];

        $this->onSuccess[] = [$this, 'processForm'];
    }

    public function validateForm(QuoteForm $form)
    {
        $values = $form->getValues();
        if (empty($values['contact']['email']) && empty($values['contact']['telegram'])) {
            $form->addError('commission.quote-form.error-no-contact');
        }
    }

    public function processForm(Form $form, $values)
    {

        $specification = new Specification();
        $specification->characterName = $values->fursuit->name;
        $specification->type = $values->fursuit->type;
        $specification->characterDescription = $values->fursuit->characterDescription;

        $this->onSave(
            $this->quote,
            $specification,
            $this->getContacts($values['contact']),
            $values->reference,
            $this
        );
    }

    private function getContacts($contactSection): array
    {
        $contacts = [];
        foreach ($contactSection as $type => $value) {
            if (!$value) {
                continue;
            }

            $contact = new Contact();
            $contact->type = $type;
            $contact->value = $value;
            $contacts[$type] = $contact;
        }

        return $contacts;
    }
}
