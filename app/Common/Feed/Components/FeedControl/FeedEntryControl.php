<?php declare(strict_types=1);

namespace PAF\Common\Feed\Components\FeedControl;

use Nette\Application\UI\Control;
use PAF\Common\Feed\FeedEvents;

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
