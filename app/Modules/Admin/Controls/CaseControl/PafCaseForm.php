<?php

namespace App\Modules\Admin\Controls\CaseControl;

use App\Common\Forms\Controls\DateInput;
use App\Common\Helpers\LocalizationHelper;
use App\Common\Model\Entity\PafCase;
use App\Common\Forms\BaseFormControl;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;

/**
 * Class PafCaseForm
 * @package App\Modules\Admin\Controls\CaseControl
 *
 * @method onSave(PafCase $case, Form $form)
 */
class PafCaseForm extends BaseFormControl
{
    /** @var PafCase */
    private $case;

    public function setEntity(PafCase $case) {
        $this->case = $case;
        $this->form()->setDefaults($case->toArray());
    }

    public function render() {
        $template = $this->createTemplate();
        $template->case = $this->case;

        $template->setFile(__DIR__ . '/pafCaseForm.latte');
        $template->render();
    }

    public function createComponentForm() {
        $form = $this->factory->create();

        $form->addGroup('fursuit-specification');
        $fursuit = $form->addContainer('fursuit');

        $fursuit->addText('name', 'paf.fursuit.name');
        $fursuit->addSelect('type', 'paf.fursuit.type', LocalizationHelper::getFursuitTypes());
        $fursuit->addTextarea('characterDescription', 'paf.fursuit.description');

        $form->addGroup('contact');
        $contact = $form->addContainer('contact');

        $contact->addText('name', 'paf.contact.name');
        $contact->addText('telegram', 'paf.contact.telegram');
        $contact->addText('email', 'paf.contact.email');

        $this->setContainerDisabled($fursuit, true);
        $this->setContainerDisabled($contact, true);

        $form->addSelect('status', 'paf.case.status', LocalizationHelper::getCaseStatuses());
        $form->addDate('targetDate', 'paf.case.target-date', DateInput::FORMAT_DATETIME)
            ->setPickerPosition(DateInput::POSITION_TOP_RIGHT);

        $form->addSubmit('submit', 'generic.update');

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }
    
    

    public function processForm(Form $form, $values) {
        $case = $this->case;

        if(count($values->fursuit)) {
            $fursuit = $this->case->getFursuit();
            $fursuit->setType($values->fursuit->type);
            $fursuit->setCharacterDescription($values->fursuit->characterDescription);
        }

        if(count($values->contact)) {
            $contact = $case->getContact();
            $contact->setTelegram($values->contact->telegram);
            $contact->setEmail($values->contact->email);
        }

        $case->setStatus($values->status);
        $case->setTargetDate($values->targetDate);

        $this->onSave($case, $form);

    }
    
    private function setContainerDisabled(Container $container, $disabled = false) {
        /** @var BaseControl $component */
        foreach ($container->getComponents() as $component) {
            $component->setDisabled($disabled);
        }
    }
}

interface IPafCaseFormFactory {
    /**
     * @return PafCaseForm
     */
    public function create();
}
