<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\Log;

use PAF\Modules\ApplicationLogModule\Entity\Event;
use PAF\Modules\ApplicationLogModule\Facade\AppLog;
use PAF\Modules\FeedModule\Components\FeedControl\FeedEntryControl;
use PAF\Modules\FeedModule\FeedEvents;

class LogFeedControl extends FeedEntryControl
{

    /** @var AppLog */
    private $logEvent;

    public function __construct(FeedEvents $events, Event $logEvent)
    {
        parent::__construct($events);
        $this->logEvent = $logEvent;
    }

    /**
     * Renders current entry for feed
     *
     * @return void
     */
    public function renderFeed(): void
    {
        $template = $this->createTemplate();
        $template->logEvent = $this->logEvent;
        $template->setFile(__DIR__ . '/logFeedControl.latte');

        $template->render();
    }
}
