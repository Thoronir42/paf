<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Repository;

use PAF\Common\Lean\BaseRepository;
use PAF\Common\Model\TransactionManager;
use PAF\Common\Workflow\ActionResult;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Model\CommissionWorkflow;
use Symfony\Component\Workflow\Transition;

class CommissionRepository extends BaseRepository
{
    private CommissionWorkflow $commissionWorkflow;
    private TransactionManager $transactionManager;

    public function injectDependencies(CommissionWorkflow $commissionWorkflow, TransactionManager $transactionManager)
    {
        $this->commissionWorkflow = $commissionWorkflow;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param string $status
     * @return Commission[]
     */
    public function getCommissionsByStatus($status = null): array
    {
        if (!$status) {
            $status = [CommissionWorkflow::STATUS_ACCEPTED, CommissionWorkflow::STATUS_WIP];
        }
        if (is_string($status)) {
            $status = [$status];
        }

        $query = $this->select('c.*', 'c', [
            'c.status' => $status
        ]);
        $query->orderBy('c.accepted_on');

        return $this->createEntities($query->fetchAll());
    }

    public function executeAction(Commission $commission, string $action): ActionResult
    {
        if (!$this->commissionWorkflow->can($commission, $action)) {
            $availableActions = array_map(function (Transition $transition) {
                return $transition->getName();
            }, $this->commissionWorkflow->getEnabledTransitions($commission));
            return ActionResult::illegalAction($commission->status, $action, $availableActions);
        }

        $result = $this->transactionManager->execute(function () use ($commission, $action) {
            $result = $this->commissionWorkflow->apply($commission, $action);
            $this->persist($commission);

            return $result;
        });

        return new ActionResult(true);
    }
}
