<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

use Nette\Utils\Paginator;
use PAF\Common\AuditTrail\Facade\AuditTrailService;
use PAF\Common\Lean\LeanMapperDataSource;
use PAF\Common\Model\TransactionManager;
use PAF\Common\Workflow\ActionResult;
use PAF\Common\AuditTrail\Repository\EntryRepository;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommissionModule\Repository\PafCaseRepository;
use PAF\Common\Feed\Service\FeedService;
use PAF\Modules\CommonModule\Repository\CommentRepository;
use SeStep\Moment\HasMomentProvider;

class PafCases
{
    use HasMomentProvider;

    /** @var PafCaseRepository */
    private $caseRepository;
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
        PafCaseRepository $caseRepository,
        CommentRepository $commentRepository,
        EntryRepository $entryRepository,
        FeedService $feedService,
        TransactionManager $transactionManager,
        AuditTrailService $auditTrailService
    ) {
        $this->caseRepository = $caseRepository;
        $this->commentRepository = $commentRepository;
        $this->entryRepository = $entryRepository;
        $this->feedService = $feedService;
        $this->transactionManager = $transactionManager;
        $this->auditTrailService = $auditTrailService;
    }

    public function save(PafCase $case)
    {
        $this->caseRepository->persist($case);
    }

    /**
     * @param string[] $status
     *
     * @return PafCase[]
     */
    public function getCasesByStatus(array $status)
    {
        return $this->caseRepository->getCasesByStatus($status);
    }

    public function getCaseFeed(PafCase $case, Paginator $paginator = null)
    {
        $entries = $this->feedService->fetchEntries([
            'comment' => $this->commentRepository->getCommentFeedQuery($case->comments),
            'logEvent' => $this->entryRepository->getEventFeedQuery($case->id),
        ], $paginator);

        return $this->feedService->hydrateFeed($entries, [
            'comment' => [$this->commentRepository, 'find'],
            'logEvent' => [$this->entryRepository, 'find'],
        ]);
    }

    public function find($id): ?PafCase
    {
        return $this->caseRepository->find($id);
    }

    public function setArchived(PafCase $case, bool $archived): bool
    {
        $result = $this->transactionManager->execute(function () use ($case, $archived) {
            if ($archived) {
                if ($case->archivedOn) {
                    return 'commission.error.caseAlreadyArchived';
                }
                $case->archivedOn = $this->momentProvider->now();
                $this->auditTrailService->addEvent($case->id, 'commission.log.caseArchived');
            } else {
                if (!$case->archivedOn) {
                    return 'commission.error.caseNotArchived';
                }

                $case->archivedOn = null;
                $this->auditTrailService->addEvent($case->id, 'commission.log.caseUnarchived');
            }
            $this->auditTrailService->omitSubject($case->id, 1);
            $this->caseRepository->persist($case);

            return null;
        });
        if ($result) {
            return false;
        }

        return true;
    }

    public function executeAction(PafCase $case, string $action): ActionResult
    {
        return $this->caseRepository->executeAction($case, $action);
    }

    public function getCasesDataSource(): LeanMapperDataSource
    {
        return $this->caseRepository->getEntityDataSource();
    }
}
