<?php declare(strict_types=1);

namespace SeStep\NetteAuditTrail\Components\FeedControl;

use SeStep\NetteAuditTrail\Entity\Entry;
use PAF\Modules\CommonModule\Latte\UserFilter;
use SeStep\NetteFeed\Components\FeedControl\FeedEntryControl;
use SeStep\NetteFeed\Components\FeedControl\FeedEntryControlFactory;
use SeStep\NetteFeed\FeedEvents;
use SeStep\NetteFeed\Model\FeedEntry;

class AuditTrailFeedControlFactory implements FeedEntryControlFactory
{
    private array $typeToTemplateFileMap = [];
    private array $matcherToTemplateFileMap = [];

    private UserFilter $userFilter;

    public function __construct(UserFilter $userFilter, array $templateByType = [])
    {
        foreach ($templateByType as $type => $templateFile) {
            $this->registerTemplate($type, $templateFile);
        }

        $this->userFilter = $userFilter;
    }

    public function registerTemplate($feedType, string $templateFile)
    {
        if (strpos($feedType, '*')) {
            $this->matcherToTemplateFileMap[] = [
                'mask' => str_replace('*', '\w', $feedType),
                'file' => $templateFile,
            ];
        } else {
            $this->typeToTemplateFileMap[$feedType] = $templateFile;
        }
    }

    public function create(FeedEvents $events, FeedEntry $entry): FeedEntryControl
    {
        /** @var Entry $auditEntry */
        $auditEntry = $entry->getSource();
        $control = new AuditTrailFeedControl($events, $auditEntry, $this->userFilter);
        if ($template = $this->getTemplateByType($auditEntry->type)) {
            $control->setTemplateFile($template);
        }


        return $control;
    }

    private function getTemplateByType(string $type): ?string
    {
        if (isset($this->typeToTemplateFileMap[$type])) {
            return $this->typeToTemplateFileMap[$type];
        }

        foreach ($this->matcherToTemplateFileMap as $matcher) {
            $mask = "/$matcher[mask]/";
            if (preg_match($mask, $type)) {
                return $matcher['file'];
            }
        }

        return null;
    }
}
