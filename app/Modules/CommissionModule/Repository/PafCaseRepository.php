<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Repository;

use LeanMapper\Connection;
use LeanMapper\IEntityFactory;
use LeanMapper\IMapper;
use PAF\Common\Lean\BaseRepository;
use PAF\Common\Model\TransactionManager;
use PAF\Common\Workflow\ActionResult;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommissionModule\Model\PafCaseWorkflow;
use Symfony\Component\Workflow\Transition;

class PafCaseRepository extends BaseRepository
{
    /** @var PafCaseWorkflow */
    private $caseWorkflow;
    /** @var TransactionManager */
    private $transactionManager;

    public function __construct(
        Connection $connection,
        IMapper $mapper,
        IEntityFactory $entityFactory,
        PafCaseWorkflow $caseWorkflow,
        TransactionManager $transactionManager,
        string $index = null
    ) {
        parent::__construct($connection, $mapper, $entityFactory, $index);
        $this->caseWorkflow = $caseWorkflow;
        $this->transactionManager = $transactionManager;
    }


    public function getCasesByStatus($status = null)
    {
        if (!$status) {
            $status = [PafCaseWorkflow::STATUS_ACCEPTED, PafCaseWorkflow::STATUS_WIP];
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

    public function executeAction(PafCase $case, string $action): ActionResult
    {
        if (!$this->caseWorkflow->can($case, $action)) {
            $availableActions = array_map(function (Transition $transition) {
                return $transition->getName();
            }, $this->caseWorkflow->getEnabledTransitions($case));
            return ActionResult::illegalAction($case->status, $action, $availableActions);
        }

        $result = $this->transactionManager->execute(function () use ($case, $action) {
            $result = $this->caseWorkflow->apply($case, $action);
            $this->persist($case);

            return $result;
        });

        return new ActionResult(true);
    }
}
