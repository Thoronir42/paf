<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\FeedControl;

use Nette\InvalidStateException;
use PAF\Modules\FeedModule\FeedEvents;
use PAF\Modules\FeedModule\Model\FeedEntry;

class FeedEntryControlFactory
{
    /** @var array */
    private $typeToClass;

    /**
     * @param array $typeToClass
     */
    public function __construct(array $typeToClass)
    {
        $this->typeToClass = $typeToClass;
    }

    public function create(FeedEvents $events, FeedEntry $entry): FeedEntryControl
    {
        $type = $entry->getType();
        $class = $this->typeToClass[$type] ?? null;

        if (!$class) {
            throw new InvalidStateException("Feed type '$type' does not have a control class associated");
        }

        return new $class($events, $entry->getSource());
    }
}
