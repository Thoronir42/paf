<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule;

use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;
use PAF\Common\Workflow\WorkflowUtils;
use PAF\Modules\CommissionModule\Model\PafCaseWorkflow;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;

class CommissionModuleExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $this->registerPafCaseWorkflowDefinition($builder);
    }

    private function registerPafCaseWorkflowDefinition(ContainerBuilder $builder)
    {
        $caseWorkflowDefinition = $builder->addDefinition($this->prefix('pafCaseWorkflowDefinition'))
            ->setType(Definition::class)
            ->setArgument('places', PafCaseWorkflow::getStates())
            ->setArgument('transitions', PafCaseWorkflow::getTransitions())
            ->setArgument('initialPlaces', [PafCaseWorkflow::STATUS_ACCEPTED])
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('pafCaseWorkflow'))
            ->setType(PafCaseWorkflow::class);
    }
}
