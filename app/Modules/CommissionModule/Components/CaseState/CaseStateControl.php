<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CaseState;

use Nette\Application\UI\Control;
use PAF\Common\Forms\FormFactory;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommissionModule\Model\PafCaseWorkflow;

/**
 * Class CaseStateControl
 * @package PAF\Modules\CommissionModule\Components\CaseState
 *
 * @method onAction(string $action)
 */
class CaseStateControl extends Control
{

    public $onAction = [];

    /** @var PafCase */
    private $case;

    /** @var FormFactory */
    private $formFactory;
    /** @var PafCaseWorkflow */
    private $caseWorkflow;

    public function __construct(PafCase $case, FormFactory $formFactory, PafCaseWorkflow $caseWorkflow)
    {
        $this->case = $case;
        $this->formFactory = $formFactory;
        $this->caseWorkflow = $caseWorkflow;
    }

    public function render()
    {
        $template = $this->createTemplate();

        $template->currentState = $this->case->status;

        $template->setFile(__DIR__ . '/caseStateControl.latte');
        $template->render();
    }

    public function createComponentActionForm()
    {
        $form = $this->formFactory->create();
        $form->addSelect('action')
            ->setPrompt('paf.workflow.actionPrompt')
            ->setItems($this->caseWorkflow->getActionsLocalized($this->case));

        $form->addSubmit('submit', 'paf.workflow.actionExecute');

        $form->onSuccess[] = function ($form, $values) {
            $this->onAction($values['action']);
        };

        return $form;
    }
}