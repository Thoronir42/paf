<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

use Nette\Utils\Paginator;
use PAF\Modules\ApplicationLogModule\Repository\EventRepository;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommissionModule\Repository\PafCaseRepository;
use PAF\Modules\FeedModule\Service\FeedService;
use SeStep\Commentable\Lean\Repository\CommentRepository;

class PafCases
{
    /** @var PafCaseRepository */
    private $caseRepository;
    /** @var CommentRepository */
    private $commentRepository;
    /** @var EventRepository */
    private $eventRepository;
    /** @var FeedService */
    private $feedService;

    public function __construct(
        PafCaseRepository $caseRepository,
        CommentRepository $commentRepository,
        EventRepository $eventRepository,
        FeedService $feedService
    ) {
        $this->caseRepository = $caseRepository;
        $this->commentRepository = $commentRepository;
        $this->eventRepository = $eventRepository;
        $this->feedService = $feedService;
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
            'logEvent' => $this->eventRepository->getEventFeedQuery($case->id),
        ], $paginator);

        return $this->feedService->hydrateFeed($entries, [
            'comment' => [$this->commentRepository, 'find'],
            'logEvent' => [$this->eventRepository, 'find'],
        ]);
    }

    public function find($id): ?PafCase
    {
        return $this->caseRepository->find($id);
    }
}