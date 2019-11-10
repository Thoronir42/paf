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
        $actions = [];
        foreach ($this->caseWorkflow->getEnabledTransitions($this->case) as $transition) {
            $actions[$transition->getName()] = $transition->getName();
        }

        $form = $this->formFactory->create();
        $form->addSelect('action')
            ->setPrompt('Do...')
            ->setItems($actions);

        $form->addSubmit('submit', 'Execute');

        $form->onSuccess[] = function ($form, $values) {
            $this->onAction($values['action']);
        };

        return $form;
    }
}
