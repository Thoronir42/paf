<?php declare(strict_types=1);

namespace PAF\Modules\DirectoryModule\Services;

use InvalidArgumentException;
use Nette\InvalidStateException;
use PAF\Common\Model\Exceptions\TransactionFailedException;
use PAF\Common\Model\TransactionManager;
use PAF\Modules\DirectoryModule\Model\Contact;
use PAF\Modules\DirectoryModule\Model\Person;
use PAF\Modules\DirectoryModule\Repository\ContactRepository;
use PAF\Modules\DirectoryModule\Repository\PersonRepository;

class PersonService
{
    /** @var TransactionManager */
    private $transactionManager;
    /** @var PersonRepository */
    private $personRepository;
    /** @var ContactRepository */
    private $contactRepository;

    public function __construct(
        TransactionManager $transactionManager,
        PersonRepository $repository,
        ContactRepository $contactRepository
    ) {
        $this->transactionManager = $transactionManager;
        $this->personRepository = $repository;
        $this->contactRepository = $contactRepository;
    }


    /**
     * @param Contact[] $contacts
     * @return Person
     *
     * @throws TransactionFailedException
     */
    public function createPersonByContacts(array $contacts): Person
    {
        $person = $this->personRepository->findByContact($contacts);
        if ($person) {
            if ($person->user) {
                throw new InvalidStateException("Person with this contact information already exists");
            }
        }

        return $this->transactionManager->execute(function () use ($person, $contacts) {
            if (!$person) {
                $person = new Person();
                $person->displayName = $this->getDisplayNameByContacts($contacts);
                $this->personRepository->persist($person);
            }

            foreach ($contacts as $contact) {
                if (!$person->contactExists($contact)) {
                    $contact->person = $person;
                    $this->contactRepository->persist($contact);
                }
            }

            return $person;
        });
    }

    /**
     * @param Contact[] $contacts
     * @return string
     */
    private function getDisplayNameByContacts(array $contacts)
    {
        if (empty($contacts)) {
            throw new InvalidArgumentException("Contacts array must not be empty");
        }

        foreach ([Contact::TYPE_TELEGRAM, Contact::TYPE_EMAIL] as $type) {
            if (isset($contacts[$type])) {
                return $contacts[$type]->value;
            }
        }

        $defaultContact = current($contacts);

        return $defaultContact->value;
    }
}
