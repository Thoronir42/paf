<?php declare(strict_types=1);

namespace PAF\Modules\ApplicationLogModule\Facade;

use Nette\Security\User;
use Nette\Utils\Json;
use PAF\Modules\ApplicationLogModule\Entity\Event;
use PAF\Modules\ApplicationLogModule\Repository\EventRepository;
use PAF\Utils\Moment\MomentProvider;

class AppLog
{
    /** @var User */
    private $user;
    /** @var MomentProvider */
    private $momentProvider;
    /** @var EventRepository */
    private $eventRepository;

    public function __construct(User $user, MomentProvider $momentProvider, EventRepository $eventRepository)
    {
        $this->user = $user;
        $this->momentProvider = $momentProvider;
        $this->eventRepository = $eventRepository;
    }

    public function addEvent(string $subject, string $type, array $parameters = [])
    {
        $event = new Event();
        $event->instant = $this->momentProvider->now();
        $event->actor = $this->user->id;

        $event->subject = $subject;
        $event->type = $type;
        $event->parameters = $parameters;

        $this->eventRepository->persist($event);
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
