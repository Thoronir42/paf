<?php declare(strict_types=1);

namespace PAF\Modules\ApplicationLogModule\Facade;

abstract class RepositoryAppLogAdapter
{
    /** @var AppLog */
    protected $appLog;

    public function __construct(AppLog $appLog)
    {
        $this->appLog = $appLog;
    }

    /**
     * @return callable[] Array of callbacks with keys being event types of {@link \LeanMapper\Events}
     */
    abstract public function getEvents(): array;
}
