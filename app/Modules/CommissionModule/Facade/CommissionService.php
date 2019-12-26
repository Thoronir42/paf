<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

use Nette\Utils\Paginator;
use PAF\Common\AuditTrail\Facade\AuditTrailService;
use PAF\Common\Lean\LeanMapperDataSource;
use PAF\Common\Model\TransactionManager;
use PAF\Common\Workflow\ActionResult;
use PAF\Common\AuditTrail\Repository\EntryRepository;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Repository\CommissionRepository;
use PAF\Common\Feed\Service\FeedService;
use PAF\Modules\CommonModule\Repository\CommentRepository;
use SeStep\Moment\HasMomentProvider;

class CommissionService
{
    use HasMomentProvider;

    /** @var CommissionRepository */
    private $commissionRepository;
    /** @var CommentRepository */
    private $commentRepository;
    /** @var EntryRepository */
    private $entryRepository;
    /** @var FeedService */
    private $feedService;
    /** @var TransactionManager */
    private $transactionManager;
    /** @var AuditTrailService */
    private $auditTrailService;

    public function __construct(
        CommissionRepository $commissionRepository,
        CommentRepository $commentRepository,
        EntryRepository $entryRepository,
        FeedService $feedService,
        TransactionManager $transactionManager,
        AuditTrailService $auditTrailService
    ) {
        $this->commissionRepository = $commissionRepository;
        $this->commentRepository = $commentRepository;
        $this->entryRepository = $entryRepository;
        $this->feedService = $feedService;
        $this->transactionManager = $transactionManager;
        $this->auditTrailService = $auditTrailService;
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
        $entries = $this->feedService->fetchEntries([
            'comment' => $this->commentRepository->getCommentFeedQuery($commission->comments),
            'logEvent' => $this->entryRepository->getEventFeedQuery($commission->id),
        ], $paginator);

        return $this->feedService->hydrateFeed($entries, [
            'comment' => [$this->commentRepository, 'find'],
            'logEvent' => [$this->entryRepository, 'find'],
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
}
