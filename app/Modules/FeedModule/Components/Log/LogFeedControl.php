<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\Log;

use PAF\Modules\AuditTrailModule\Entity\Entry;
use PAF\Modules\FeedModule\Components\FeedControl\FeedEntryControl;
use PAF\Modules\FeedModule\FeedEvents;

class LogFeedControl extends FeedEntryControl
{

    /** @var Entry */
    private $entry;

    /** @var string */
    private $templateFile = __DIR__ . '/logFeedControl.latte';

    public function __construct(FeedEvents $events, Entry $entry)
    {
        parent::__construct($events);
        $this->entry = $entry;
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
