<?php declare(strict_types=1);

namespace SeStep\NetteFeed\Components\FeedControl;

use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use SeStep\NetteFeed\FeedEvents;

abstract class FeedEntryControl extends Control
{
    protected FeedEvents $events;

    public function __construct(FeedEvents $events)
    {
        $this->events = $events;
    }

    protected function createTemplate(): ITemplate
    {
        $template = parent::createTemplate();
        $template->layout = __DIR__ . '/@feedEntry.latte';

        return $template;
    }

    /**
     * Renders current entry for feed
     *
     * @return void
     */
    abstract public function renderFeed(): void;
}
