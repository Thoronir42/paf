<?php declare(strict_types=1);

namespace PAF\Modules\AuditTrailModule\Facade;

use Nette\Security\User;
use PAF\Modules\AuditTrailModule\Entity\Entry;
use PAF\Modules\AuditTrailModule\Repository\EntryRepository;
use PAF\Utils\Moment\MomentProvider;

class AuditTrailService
{
    /** @var User */
    private $user;
    /** @var MomentProvider */
    private $momentProvider;
    /** @var EntryRepository */
    private $entryRepository;

    private $omittedSubjects = [];

    public function __construct(User $user, MomentProvider $momentProvider, EntryRepository $entryRepository)
    {
        $this->user = $user;
        $this->momentProvider = $momentProvider;
        $this->entryRepository = $entryRepository;
    }

    public function addEvent(string $subject, string $type, array $parameters = [])
    {
        if (isset($this->omittedSubjects[$subject])) {
            return;
        }

        $event = new Entry();
        $event->instant = $this->momentProvider->now();
        $event->setActorId($this->user->id);

        $event->subject = $subject;
        $event->type = $type;
        $event->parameters = $parameters;

        $this->entryRepository->persist($event);
    }

    public function normalizeValues($parameters)
    {
        foreach ($parameters as &$parameter) {
            $this->normalizeValue($parameter['newValue']);
            if (isset($parameter['oldValue'])) {
                $this->normalizeValue($parameter['oldValue']);
            }
        }

        return $parameters;
    }

    private function normalizeValue(&$value)
    {
        if (is_scalar($value) || $value === null) {
            return 0;
        }

        if ($value instanceof \DateTime) {
            $value = $value->format('c');
            return 1;
        }

        trigger_error('Value of type ' . gettype($value) . ' not normalized');
        return -1;
    }

    /**
     * Sets omission counter for given subject.
     *
     * @param string $subject
     * @param int $omit
     *
     * @return bool
     */
    public function omitSubject(string $subject, int $omit = PHP_INT_MAX): bool
    {
        if ($omit > 0) {
            $this->omittedSubjects[$subject] = $omit;
            return true;
        } else {
            unset($this->omittedSubjects[$subject]);
            return false;
        }
    }

    /**
     * Tests whether subject should be omitted from trailing
     *
     * @param string $subject
     *
     * @return bool
     */
    protected function checkOmission(string $subject): bool
    {
        if (!isset($this->omittedSubjects[$subject])) {
            return false;
        }
        $omissionCount = $this->omittedSubjects[$subject];
        if ($omissionCount <= 0) {
            unset($this->omittedSubjects[$subject]);
            return false;
        }
        $this->omittedSubjects[$subject] = $omissionCount - 1;
        return true;
    }
}
