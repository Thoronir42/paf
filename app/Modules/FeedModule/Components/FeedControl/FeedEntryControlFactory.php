<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\FeedControl;

use PAF\Modules\FeedModule\FeedEvents;
use PAF\Modules\FeedModule\Model\FeedEntry;

interface FeedEntryControlFactory
{
    public function create(FeedEvents $events, FeedEntry $entry): FeedEntryControl;
}
