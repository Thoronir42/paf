<?php declare(strict_types=1);

namespace PAF\Common\Feed\Components\FeedControl;

use PAF\Common\Feed\FeedEvents;
use PAF\Common\Feed\Model\FeedEntry;

interface FeedEntryControlFactory
{
    public function create(FeedEvents $events, FeedEntry $entry): FeedEntryControl;
}
