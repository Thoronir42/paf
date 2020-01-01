<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

use Nette\Http\FileUpload;
use Nette\Utils\Paginator;
use PAF\Common\Model\Exceptions\TransactionFailedException;
use PAF\Common\Model\TransactionManager;
use PAF\Common\Storage\PafImageStorage;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommissionModule\Model\Specification;
use PAF\Modules\CommissionModule\Repository\QuoteRepository;
use PAF\Modules\CommissionModule\Repository\SpecificationRepository;
use PAF\Modules\CommonModule\Repository\SlugRepository;
use PAF\Modules\DirectoryModule\Model\Person;
use SeStep\Moment\HasMomentProvider;
use UnexpectedValueException;

class QuoteService
{
    use HasMomentProvider;

    /** @var callable[] */
    public $onQuoteAccept = [];

    /** @var TransactionManager */
    private $transactionManager;
    /** @var QuoteRepository */
    private $quoteRepository;
    /** @var SlugRepository */
    private $slugRepository;
    /** @var PafImageStorage */
    private $imageStorage;
    /** @var SpecificationRepository */
    private $specificationRepository;

    public function __construct(
        TransactionManager $transactionManager,
        QuoteRepository $quoteRepository,
        SlugRepository $slugRepository,
        PafImageStorage $imageStorage,
        SpecificationRepository $specificationRepository
    ) {
        $this->transactionManager = $transactionManager;
        $this->quoteRepository = $quoteRepository;
        $this->slugRepository = $slugRepository;
        $this->imageStorage = $imageStorage;
        $this->specificationRepository = $specificationRepository;
    }


    /**
     * @param Quote $quote
     * @param Specification $specification
     * @param Person $issuer
     * @param FileUpload[] $references
     *
     * @return string - error code
     *
     * @throws TransactionFailedException
     */
    public function createNewQuote(
        Quote $quote,
        Specification $specification,
        Person $issuer,
        $references
    ): ?string {
        return $this->transactionManager->execute(function () use ($quote, $specification, $issuer, $references) {
            $slug = $this->slugRepository->createSlug($specification->characterName, true);

            $specification->references = $this->imageStorage->createFileThread($references, 'quote', $slug->id);
            if (!$this->saveSpecification($specification)) {
                throw new UnexpectedValueException("Could not save specification");
            }

            $quote->status = Quote::STATUS_NEW;
            $quote->slug = $slug;
            $quote->specification = $specification;
            $quote->issuer = $issuer;


            $this->quoteRepository->persist($quote);
            return null;
        });
    }

    public function saveSpecification(Specification $specification): bool
    {
        $result = $this->specificationRepository->persist($specification);
        return $result > 0;
    }

    public function rejectQuote(Quote $quote)
    {
        $quote->status = Quote::STATUS_REJECTED;
        $this->quoteRepository->persist($quote);
        return true;
    }

    /**
     * @param Quote $quote
     * @return bool
     * @throws TransactionFailedException
     */
    public function acceptQuote(Quote $quote)
    {
        $this->transactionManager->execute(function () use ($quote) {
            $quote->status = Quote::STATUS_ACCEPTED;
            $this->quoteRepository->persist($quote);

            foreach ($this->onQuoteAccept as $callback) {
                call_user_func($callback, $quote);
            }
        });

        return true;
    }

    /**
     * @param Paginator|null $paginator
     *
     * @return Quote[]
     */
    public function findForOverview(Paginator $paginator = null)
    {
        return $this->quoteRepository->findForOverview($paginator);
    }
}
