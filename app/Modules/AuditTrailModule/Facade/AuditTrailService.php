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

    public function __construct(User $user, MomentProvider $momentProvider, EntryRepository $entryRepository)
    {
        $this->user = $user;
        $this->momentProvider = $momentProvider;
        $this->entryRepository = $entryRepository;
    }

    public function addEvent(string $subject, string $type, array $parameters = [])
    {
        $event = new Entry();
        $event->instant = $this->momentProvider->now();
        $event->actor = $this->user->id;

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
}
