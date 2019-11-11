<?php declare(strict_types=1);

namespace PAF\Modules\AuditTrailModule\Components\FeedControl;

use PAF\Modules\AuditTrailModule\Entity\Entry;
use PAF\Modules\CommonModule\Latte\UserFilter;
use PAF\Modules\FeedModule\Components\FeedControl\FeedEntryControl;
use PAF\Modules\FeedModule\FeedEvents;

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
        $template->actor = $this->userFilter->useFilter($this->entry->actor);

        $template->setFile($this->templateFile);

        $template->render();
    }

    public function renderDiff(array $arguments)
    {
        $prefix = $arguments['prefix'] ?? '';

        $changes = $this->entry->parameters['changes'] ?? [];

        foreach ($changes as $property => &$change) {
            $change['prop'] = ($prefix ? $prefix . '.' : '') . $change['prop'];
            $change['newValue'] = $this->presentValue($change['newValue']);
            if (isset($change['oldValue'])) {
                $change['oldValue'] = $this->presentValue($change['oldValue']);
            }
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

    private function presentValue($value)
    {
        if (is_scalar($value)) {
            return $value;
        }

        return json_encode($value);
    }
}
