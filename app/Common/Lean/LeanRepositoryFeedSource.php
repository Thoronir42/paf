<?php declare(strict_types=1);

namespace PAF\Common\Lean;

use Dibi\Fluent;
use PAF\Modules\Feed\Source\FeedSource;

class LeanRepositoryFeedSource implements FeedSource
{
    /** @var BaseRepository */
    private $repository;
    /** @var Fluent */
    private $select;

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
