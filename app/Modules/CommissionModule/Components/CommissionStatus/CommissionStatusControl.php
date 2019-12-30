<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CommissionStatus;

use Nette\Application\UI\Control;
use PAF\Common\Forms\FormFactory;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Model\CommissionWorkflow;

/**
 * @method onAction(string $action)
 * @method onArchivedChanged(bool $archive)
 */
class CommissionStatusControl extends Control
{
    /** @var callable[] */
    public $onAction = [];
    /** @var callable[] */
    public $onArchivedChanged = [];

    /** @var Commission */
    private $commission;

    /** @var FormFactory */
    private $formFactory;
    /** @var CommissionWorkflow */
    private $commissionWorkflow;

    public function __construct(
        Commission $commission,
        FormFactory $formFactory,
        CommissionWorkflow $commissionWorkflow
    ) {
        $this->commission = $commission;
        $this->formFactory = $formFactory;
        $this->commissionWorkflow = $commissionWorkflow;
    }

    public function render()
    {
        $template = $this->createTemplate();

        $template->currentState = $this->commission->status;
        $template->archived = !!$this->commission->archivedOn;

        $template->setFile(__DIR__ . '/commissionStatusControl.latte');
        $template->render();
    }

    public function createComponentActionForm()
    {
        $form = $this->formFactory->create();
        $action = $form->addSelect('action')
            ->setPrompt('paf.workflow.actionPrompt')
            ->setItems($this->commissionWorkflow->getActionsLocalized($this->commission));

        $submit = $form->addSubmit('submit', 'paf.workflow.actionExecute');

        if ($this->commission->archivedOn) {
            $action->setDisabled();
            $submit->setDisabled();
        } else {
            $form->onSuccess[] = function ($form, $values) {
                $this->onAction($values['action']);
            };
        }

        return $form;
    }

    public function handleArchive()
    {
        $this->onArchivedChanged(true);
    }

    public function handleUnarchive()
    {
        $this->onArchivedChanged(false);
    }
}
