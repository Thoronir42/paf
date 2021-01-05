<?php declare(strict_types=1);

namespace PAF\Modules\Feed\Source;

/**
 * FeedSource is a collection provider allowing to fetch entries id placeholders
 * and their hydration.
 * Using FeedSource, it is possible to collect entries from various locations
 * into single continual feed.
 */
interface FeedSource
{
    public function fetchEntries(): array;

    public function hydrateEntries(array $entries): array;
}
