<?php declare(strict_types=1);

namespace SeStep\NetteAuditTrail\Components\FeedControl;

use SeStep\NetteAuditTrail\Entity\Entry;
use PAF\Modules\CommonModule\Latte\UserFilter;
use SeStep\NetteFeed\Components\FeedControl\FeedEntryControl;
use SeStep\NetteFeed\FeedEvents;

class AuditTrailFeedControl extends FeedEntryControl
{
    private Entry $entry;
    private UserFilter $userFilter;

    private string $templateFile = __DIR__ . '/auditTrailFeedControl.latte';

    public function __construct(FeedEvents $events, Entry $entry, UserFilter $userFilter)
    {
        parent::__construct($events);
        $this->entry = $entry;
        $this->userFilter = $userFilter;
    }

    /**
     * Renders current entry for feed
     *
     * @return void
     */
    public function renderFeed(): void
    {
        $template = $this->createTemplate();
        $template->logEvent = $this->entry;
        $template->parameters = $this->entry->parameters;

        $template->actor = $this->userFilter->useFilter($this->entry->actor);

        $template->setFile($this->templateFile);

        $template->render();
    }

    public function renderPropertyDiff(string $entityClass, string $propertyName, $newValue, $oldValue = null)
    {
        $template = $this->createTemplate();
        $template->setFile(__DIR__ . '/propertyDiff.latte');
        $template->entityClass = $entityClass;
        $template->property = $propertyName;
        $template->newValue = $newValue;
        $template->oldValue = $oldValue;

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
