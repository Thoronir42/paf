<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Facade;

use Nette\Http\FileUpload;
use Nette\InvalidStateException;
use Nette\Utils\Strings;
use PAF\Common\Model\TransactionManager;
use PAF\Common\Storage\PafImageStorage;
use PAF\Modules\CommissionModule\Model\Commission;
use PAF\Modules\CommissionModule\Model\CommissionWorkflow;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommissionModule\Model\Specification;
use PAF\Modules\CommissionModule\Repository\CommissionRepository;
use PAF\Modules\CommissionModule\Repository\QuoteRepository;
use PAF\Modules\CommissionModule\Repository\SpecificationRepository;
use PAF\Modules\CommonModule\Model\Contact;
use PAF\Modules\CommonModule\Model\Person;
use PAF\Modules\CommonModule\Repository\ContactRepository;
use PAF\Modules\CommonModule\Repository\PersonRepository;
use PAF\Modules\CommonModule\Repository\SlugRepository;
use PAF\Modules\CommonModule\Services\CommentsService;
use SeStep\Moment\HasMomentProvider;

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
    /** @var PersonRepository */
    private $personRepository;
    /** @var ContactRepository */
    private $contactRepository;
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
        PersonRepository $personRepository,
        ContactRepository $contactRepository,
        SlugRepository $slugRepository,
        QuoteRepository $quoteRepository,
        PafImageStorage $imageStorage,
        CommissionRepository $commissionRepository,
        CommentsService $commentsService,
        TransactionManager $transactionManager
    ) {
        $this->specificationRepository = $specificationRepository;
        $this->personRepository = $personRepository;
        $this->contactRepository = $contactRepository;
        $this->slugRepository = $slugRepository;
        $this->quoteRepository = $quoteRepository;
        $this->imageStorage = $imageStorage;
        $this->commissionRepository = $commissionRepository;
        $this->commentsService = $commentsService;
        $this->transactionManager = $transactionManager;
    }

    /**
     * @param Quote $quote
     * @param Specification $specification
     * @param Person $issuer
     * @param FileUpload[] $references
     *
     * @return string - error code
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
                throw new \UnexpectedValueException("Could not save specification");
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

    /**
     * @param Contact[] $contacts
     * @return Person
     */
    public function createIssuerByContacts(array $contacts): Person
    {
        $issuer = $this->personRepository->findByContact($contacts);
        if ($issuer) {
            if ($issuer->user) {
                throw new InvalidStateException("Person with this contact information already exists");
            }
        } else {
            $issuer = new Person();
            $issuer->displayName = $this->getDisplayNameByContacts($contacts);
            $this->personRepository->persist($issuer);
        }


        foreach ($contacts as $contact) {
            if (!$issuer->contactExists($contact)) {
                $contact->person = $issuer;
                $this->contactRepository->persist($contact);
            }
        }

        return $issuer;
    }

    /**
     * @param Contact[] $contacts
     * @return string
     */
    private function getDisplayNameByContacts(array $contacts)
    {
        if (empty($contacts)) {
            throw new \InvalidArgumentException("Contacts array must not be empty");
        }

        foreach ([Contact::TYPE_TELEGRAM, Contact::TYPE_EMAIL] as $type) {
            if (isset($contacts[$type])) {
                return $contacts[$type]->value;
            }
        }

        $defaultContact = current($contacts);

        return $defaultContact->value;
    }

    public function rejectQuote(Quote $quote)
    {
        $quote->status = Quote::STATUS_REJECTED;
        $this->quoteRepository->persist($quote);
        return true;
    }

    public function acceptQuote(Quote $quote)
    {
        $this->transactionManager->execute(function () use ($quote) {
            $quote->status = Quote::STATUS_ACCEPTED;
            $this->quoteRepository->persist($quote);

            $commission = new Commission();
            $commission->slug = $quote->slug;
            $commission->customer = $quote->issuer;
            $commission->specification = $quote->specification;
            $commission->acceptedOn = $this->momentProvider->now();
            $commission->comments = $this->commentsService->createNewThread();

            $this->commissionRepository->persist($commission);

            return $commission;
        });

        return true;
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
