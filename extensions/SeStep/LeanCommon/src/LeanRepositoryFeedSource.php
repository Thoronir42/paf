<?php declare(strict_types=1);

namespace SeStep\LeanCommon;

use Dibi\Fluent;
use SeStep\NetteFeed\Source\FeedSource;

class LeanRepositoryFeedSource implements FeedSource
{
    private BaseRepository $repository;
    private Fluent $select;

    public function __construct(BaseRepository $repository, Fluent $select)
    {
        $this->repository = $repository;
        $this->select = $select;
    }
    
    public function fetchEntries(): array
    {
        return $this->select->fetchAll();
    }

    public function hydrateEntries(array $entries): array
    {
        return $this->repository->find($entries);
    }

    public function getQuery(): Fluent
    {
        return $this->select;
    }
}
