<?php declare(strict_types=1);

namespace PAF\Common\Feed\Source;

interface FeedSource
{
    public function fetchEntries(): array;

    public function hydrateEntries(array $entries): array;
}
