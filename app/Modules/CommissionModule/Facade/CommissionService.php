<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

use Nette\Utils\Paginator;
use SeStep\NetteAuditTrail\Facade\AuditTrailService;
use SeStep\LeanCommon\LeanMapperDataSource;
use PAF\Common\Model\TransactionManager;
use PAF\Common\Workflow\ActionResult;
use SeStep\NetteAuditTrail\Repository\EntryRepository;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Model\CommissionWorkflow;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommissionModule\Repository\CommissionRepository;
use SeStep\NetteFeed\Service\FeedService;
use PAF\Modules\CommonModule\Services\CommentsService;
use SeStep\Moment\HasMomentProvider;

class CommissionService
{
    use HasMomentProvider;

    private CommissionRepository $commissionRepository;
    private CommentsService $commentsService;
    private EntryRepository $entryRepository;
    private FeedService $feedService;
    private TransactionManager $transactionManager;
    private AuditTrailService $auditTrailService;

    public function __construct(
        CommissionRepository $commissionRepository,
        CommentsService $commentsService,
        EntryRepository $entryRepository,
        FeedService $feedService,
        TransactionManager $transactionManager,
        AuditTrailService $auditTrailService
    ) {
        $this->commissionRepository = $commissionRepository;
        $this->commentsService = $commentsService;
        $this->entryRepository = $entryRepository;
        $this->feedService = $feedService;
        $this->transactionManager = $transactionManager;
        $this->auditTrailService = $auditTrailService;
    }

    public function createFromQuote(Quote $quote)
    {
        $commission = new Commission();
        $commission->slug = $quote->slug;
        $commission->supplier = $quote->supplier;
        $commission->customer = $quote->issuer;
        $commission->specification = $quote->specification;
        $commission->acceptedOn = $this->momentProvider->now();
        $commission->comments = $this->commentsService->createNewThread();

        $this->commissionRepository->persist($commission);
    }

    public function save(Commission $commission)
    {
        $this->commissionRepository->persist($commission);
    }

    /**
     * @param string[] $status
     *
     * @return Commission[]
     */
    public function getCommissionsByStatus(array $status): array
    {
        return $this->commissionRepository->getCommissionsByStatus($status);
    }

    public function getCommissionFeed(Commission $commission, Paginator $paginator = null): array
    {
        return $this->feedService->fetchFeed([
            'comment' => $this->commentsService->getFeedSource($commission->comments),
            'logEvent' => $this->entryRepository->getFeedSource($commission->id),
        ]);
    }

    public function find($id): ?Commission
    {
        return $this->commissionRepository->find($id);
    }

    public function setArchived(Commission $commission, bool $archived): bool
    {
        $result = $this->transactionManager->execute(function () use ($commission, $archived) {
            if ($archived) {
                if ($commission->archivedOn) {
                    return 'commission.error.commissionAlreadyArchived';
                }
                $commission->archivedOn = $this->momentProvider->now();
                $this->auditTrailService->addEvent($commission->id, 'commission.log.commissionArchived');
            } else {
                if (!$commission->archivedOn) {
                    return 'commission.error.commissionNotArchived';
                }

                $commission->archivedOn = null;
                $this->auditTrailService->addEvent($commission->id, 'commission.log.commissionUnarchived');
            }
            $this->auditTrailService->omitSubject($commission->id, 1);
            $this->commissionRepository->persist($commission);

            return null;
        });
        if ($result) {
            return false;
        }

        return true;
    }

    public function executeAction(Commission $commission, string $action): ActionResult
    {
        return $this->commissionRepository->executeAction($commission, $action);
    }

    public function getCommissionsDataSource(array $conditions = null): LeanMapperDataSource
    {
        return $this->commissionRepository->getEntityDataSource($conditions);
    }

    public function countUnresolvedCommissions(): int
    {
        return $this->commissionRepository->countBy([
            '!status' => [
                CommissionWorkflow::STATUS_FINISHED,
                CommissionWorkflow::STATUS_SHIPPED,
                CommissionWorkflow::STATUS_CANCELLED,
            ],
        ]);
    }
}
