<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\PafCaseForm;

use Nette\Application\UI\ITemplate;
use PAF\Common\Forms\Controls\DateInput;
use PAF\Common\Forms\FormWrapperControl;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
use Nette\Forms\Controls\BaseControl;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommissionModule;
use PAF\Modules\PortfolioModule;
use stdClass;

/**
 * Class PafCaseForm
 * @package PAF\Modules\Admin\Controls\CaseControl
 *
 * @method ITemplate|stdClass createTemplate
 * @method onSave(PafCase $case, Form $form)
 */
class PafCaseForm extends FormWrapperControl
{
    /** @var PafCase */
    private $case;

    public function setEntity(PafCase $case)
    {
        $this->case = $case;
        $this->form()->setDefaults($case->getRowData());
    }

    public function render()
    {
        $template = $this->createTemplate();
        $template->case = $this->case;

        $template->setFile(__DIR__ . '/pafCaseForm.latte');
        $template->render();
    }

    public function createComponentForm()
    {
        $form = $this->factory->create();

        $form->addGroup('fursuit-specification');
        $fursuit = $form->addContainer('fursuit');

        $fursuit->addText('name', 'paf.fursuit.name');
        $fursuit->addSelect('type', 'paf.fursuit.type', PortfolioModule\Localization::getFursuitTypes());
        $fursuit->addTextarea('characterDescription', 'paf.fursuit.description');

        $form->addGroup('contact');
        $contact = $form->addContainer('contact');

        $contact->addText('name', 'paf.contact.name');
        $contact->addText('telegram', 'paf.contact.telegram');
        $contact->addText('email', 'paf.contact.email');

        $this->setContainerDisabled($fursuit, true);
        $this->setContainerDisabled($contact, true);

        $form->addSelect('status', 'paf.case.status', CommissionModule\Localization::getCaseStatuses());
        $form->addDate('targetDate', 'paf.case.target-date', DateInput::FORMAT_DATETIME)
            ->setPickerPosition(DateInput::POSITION_TOP_RIGHT);

        $form->addSubmit('submit', 'generic.update');

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }


    public function processForm(Form $form, $values)
    {
        $case = $this->case;

        if (count($values->fursuit)) {
            $specification = $this->case->specification;
            $specification->type = $values->fursuit->type;
            $specification->characterDescription = $values->fursuit->characterDescription;
        }

        if (count($values->contact)) {
            $case->contact->telegram = $values->contact->telegram;
            $case->contact->email = $values->contact->email;
        }

        $case->status = $values->status;
        $case->targetDate = $values->targetDate;

        $this->onSave($case, $form);

    }

    private function setContainerDisabled(Container $container, $disabled = false)
    {
        /** @var BaseControl $component */
        foreach ($container->getComponents() as $component) {
            $component->setDisabled($disabled);
        }
    }
}

interface IPafCaseFormFactory
{
    /**
     * @return PafCaseForm
     */
    public function create();
}
