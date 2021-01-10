<?php declare(strict_types=1);

namespace SeStep\NetteFeed\Components\FeedControl;

use SeStep\NetteFeed\FeedEvents;
use SeStep\NetteFeed\Model\FeedEntry;

interface FeedEntryControlFactory
{
    public function create(FeedEvents $events, FeedEntry $entry): FeedEntryControl;
}
