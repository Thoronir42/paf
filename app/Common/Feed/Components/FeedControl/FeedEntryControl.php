<?php declare(strict_types=1);

namespace PAF\Common\Feed\Components\FeedControl;

use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Bridges\ApplicationLatte\Template;
use PAF\Common\Feed\FeedEvents;

abstract class FeedEntryControl extends Control
{

    /** @var FeedEvents */
    protected $events;

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
