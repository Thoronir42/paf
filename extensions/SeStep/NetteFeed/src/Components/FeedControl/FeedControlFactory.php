<?php declare(strict_types=1);

namespace SeStep\NetteFeed\Components\FeedControl;

class FeedControlFactory
{
    private EntryControlFactory $entryControlFactory;

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
