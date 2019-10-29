<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\FeedControl;

class FeedControlFactory
{
    /** @var EntryControlFactory */
    private $entryControlFactory;

    public function __construct(EntryControlFactory $entryControlFactory)
    {
        $this->entryControlFactory = $entryControlFactory;
    }

    public function create(array $feed): FeedControl
    {
        $control = new FeedControl($this->entryControlFactory, $feed);

        return $control;
    }
}
