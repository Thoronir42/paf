<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\Log;

use PAF\Modules\ApplicationLogModule\Entity\Event;
use PAF\Modules\FeedModule\Components\FeedControl\FeedEntryControl;
use PAF\Modules\FeedModule\FeedEvents;

class LogFeedControl extends FeedEntryControl
{

    /** @var Event */
    private $logEvent;

    /** @var string */
    private $templateFile = __DIR__ . '/logFeedControl.latte';

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
        $template->setFile($this->templateFile);

        $template->render();
    }

    public function renderDiff(array $arguments)
    {
        $prefix = $arguments['prefix'] ?? '';

        $changes = [];
        foreach ($this->logEvent->parameters['properties'] as $property) {
            $changes[] = [
                'name' => $prefix ? $prefix . '.' . $property : $property,
                'newValue' => null,
                'oldValue' => null,
            ];
        }

        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/propertyChanges.latte');
        $template->changes = $changes;

        $template->render();
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile(string $templateFile): void
    {
        $this->templateFile = $templateFile;
    }
}
