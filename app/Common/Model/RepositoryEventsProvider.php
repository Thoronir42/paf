<?php declare(strict_types=1);

namespace PAF\Common\Model;

interface RepositoryEventsProvider
{
    /**
     * @return callable[] Array of callbacks with keys being event types of {@link \LeanMapper\Events}
     */
    public function getEvents(): array;
}
