<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\FeedControl;

use Nette\Application\UI\Control;
use PAF\Modules\FeedModule\FeedEvents;

abstract class FeedEntryControl extends Control
{

    /** @var FeedEvents */
    protected $events;

    public function __construct(FeedEvents $events)
    {
        $this->events = $events;
    }

    /**
     * Renders current entry for feed
     *
     * @return void
     */
    abstract public function renderFeed(): void;
}
