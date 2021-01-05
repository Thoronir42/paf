<?php declare(strict_types=1);

namespace PAF\Common\AuditTrail\Components\FeedControl;

use PAF\Common\AuditTrail\Entity\Entry;
use PAF\Modules\CommonModule\Latte\UserFilter;
use PAF\Modules\Feed\Components\FeedControl\FeedEntryControl;
use PAF\Modules\Feed\FeedEvents;

class AuditTrailFeedControl extends FeedEntryControl
{

    /** @var Entry */
    private $entry;
    /** @var UserFilter */
    private $userFilter;

    /** @var string */
    private $templateFile = __DIR__ . '/auditTrailFeedControl.latte';

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
