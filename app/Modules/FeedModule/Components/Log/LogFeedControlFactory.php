<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\Log;

use PAF\Modules\AuditTrailModule\Entity\Entry;
use PAF\Modules\FeedModule\Components\FeedControl\FeedEntryControl;
use PAF\Modules\FeedModule\Components\FeedControl\FeedEntryControlFactory;
use PAF\Modules\FeedModule\FeedEvents;
use PAF\Modules\FeedModule\Model\FeedEntry;

class LogFeedControlFactory implements FeedEntryControlFactory
{

    /** @var array */
    private $templateByType;

    public function __construct(array $templateByType)
    {
        $this->templateByType = $templateByType;
    }

    public function create(FeedEvents $events, FeedEntry $entry): FeedEntryControl
    {
        /** @var Entry $auditEntry */
        $auditEntry = $entry->getSource();
        $control = new LogFeedControl($events, $auditEntry);
        if ($template = $this->getTemplateByType($auditEntry->type)) {
            $control->setTemplateFile($template);
        }


        return $control;
    }

    private function getTemplateByType(string $type): ?string
    {
        return $this->templateByType[$type] ?? null;
    }
}
