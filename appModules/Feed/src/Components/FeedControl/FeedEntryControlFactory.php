<?php declare(strict_types=1);

namespace PAF\Modules\Feed\Components\FeedControl;

use PAF\Modules\Feed\FeedEvents;
use PAF\Modules\Feed\Model\FeedEntry;

interface FeedEntryControlFactory
{
    public function create(FeedEvents $events, FeedEntry $entry): FeedEntryControl;
}
