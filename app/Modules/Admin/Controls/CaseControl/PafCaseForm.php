<?php

namespace App\Modules\Admin\Controls\CaseControl;

use App\Common\Helpers\LocalizationHelper;
use App\Common\Model\Entity\Fursuit;
use App\Common\Model\Entity\PafCase;
use App\Common\Controls\Forms\BaseFormControl;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;


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

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }
    
    

    public function processForm(Form $form, $values) {
        dump($this->case);
        dump($values); exit;
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
