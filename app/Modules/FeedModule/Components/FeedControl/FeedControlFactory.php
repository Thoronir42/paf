<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\FeedControl;

class FeedControlFactory
{
    /** @var FeedEntryControlFactory */
    private $entryControlFactory;

    public function __construct(FeedEntryControlFactory $entryControlFactory)
    {
        $this->entryControlFactory = $entryControlFactory;
    }

    public function create(array $feed): FeedControl
    {
        $control = new FeedControl($this->entryControlFactory, $feed);

        return $control;
    }
}
