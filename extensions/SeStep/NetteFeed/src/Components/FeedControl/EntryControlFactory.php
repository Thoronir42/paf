<?php declare(strict_types=1);

namespace SeStep\NetteFeed\Components\FeedControl;

use Nette\InvalidStateException;
use SeStep\NetteFeed\FeedEvents;
use SeStep\NetteFeed\Model\FeedEntry;

/**
 * @internal
 */
class EntryControlFactory
{
    private array $controlTypes;

    /**
     * @param array $controlTypes
     */
    public function __construct(array $controlTypes)
    {
        $this->controlTypes = $controlTypes;
    }

    public function create(FeedEvents $events, FeedEntry $entry): FeedEntryControl
    {
        $type = $entry->getType();
        $control = $this->controlTypes[$type] ?? null;

        if (!$control) {
            throw new InvalidStateException("Feed type '$type' does not have a control class or factory associated");
        }

        if (is_object($control)) {
            if ($control instanceof FeedEntryControlFactory) {
                return $control->create($events, $entry);
            }

            $wrongType = 'instance of ' . get_class($control);
        } elseif (is_string($control) && is_a($control, FeedEntryControl::class, true)) {
            return new $control($events, $entry->getSource());
        } else {
            $wrongType = gettype($control) . "($control)";
        }

        throw new \UnexpectedValueException("Descendant class FQN of " . FeedEntryControl::class
            . ",or factory instance expected for type '$type', got: " . $wrongType);
    }
}
