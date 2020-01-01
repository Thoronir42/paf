<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

use Nette\Http\FileUpload;
use PAF\Common\Model\Exceptions\TransactionFailedException;
use PAF\Common\Model\TransactionManager;
use PAF\Common\Storage\PafImageStorage;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Model\CommissionWorkflow;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommissionModule\Model\Specification;
use PAF\Modules\CommissionModule\Repository\CommissionRepository;
use PAF\Modules\CommissionModule\Repository\QuoteRepository;
use PAF\Modules\CommissionModule\Repository\SpecificationRepository;
use PAF\Modules\DirectoryModule\Model\Person;
use PAF\Modules\CommonModule\Repository\SlugRepository;
use PAF\Modules\CommonModule\Services\CommentsService;
use SeStep\Moment\HasMomentProvider;
use UnexpectedValueException;

/**
 * Class Commissions
 *
 * todo: split up into smaller parts
 */
class Commissions
{
    use HasMomentProvider;

    /** @var SpecificationRepository */
    private $specificationRepository;
    /** @var SlugRepository */
    private $slugRepository;
    /** @var QuoteRepository */
    private $quoteRepository;
    /** @var CommissionRepository */
    private $commissionRepository;
    /** @var PafImageStorage */
    private $imageStorage;
    /** @var CommentsService */
    private $commentsService;
    /** @var TransactionManager */
    private $transactionManager;

    public function __construct(
        SpecificationRepository $specificationRepository,
        SlugRepository $slugRepository,
        QuoteRepository $quoteRepository,
        PafImageStorage $imageStorage,
        CommissionRepository $commissionRepository,
        CommentsService $commentsService,
        TransactionManager $transactionManager
    ) {
        $this->specificationRepository = $specificationRepository;
        $this->slugRepository = $slugRepository;
        $this->quoteRepository = $quoteRepository;
        $this->imageStorage = $imageStorage;
        $this->commissionRepository = $commissionRepository;
        $this->commentsService = $commentsService;
        $this->transactionManager = $transactionManager;
    }



    public function countUnresolvedQuotes(): int
    {
        return $this->quoteRepository->countBy([
            'status' => [Quote::STATUS_NEW],
        ]);
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
