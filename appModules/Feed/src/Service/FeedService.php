<?php declare(strict_types=1);

namespace PAF\Modules\Feed\Service;

use Dibi\Connection;
use Dibi\Fluent;
use InvalidArgumentException;
use Nette\Utils\Paginator;
use PAF\Modules\Feed\Model\FeedEntry;
use PAF\Modules\Feed\Model\FeedEntryAdapter;
use PAF\Modules\Feed\Source\FeedSource;
use PAF\Common\Lean\LeanRepositoryFeedSource;

class FeedService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param FeedSource[] $sources
     *
     * @return FeedEntry[]
     */
    public function fetchFeed(array $sources): array
    {
        $queries = [];
        $hydrateCallbacks = [];
        foreach ($sources as $name => $source) {
            if ($source instanceof LeanRepositoryFeedSource) {
                $queries[$name] = $source->getQuery();
                $hydrateCallbacks[$name] = [$source, 'hydrateEntries'];
            } else {
                $sourceType = get_class($source);
                trigger_error("Unsupported source type '$sourceType'");
            }
        }

        $entries = $this->sqlFetchEntries($queries);

        return $this->hydrateFeed($entries, $hydrateCallbacks);
    }

    /**
     * @param Fluent[] $queries
     * @param Paginator|null $paginator
     *
     * @return array[]
     */
    public function sqlFetchEntries(array $queries, Paginator $paginator = null): array
    {
        if (empty($queries)) {
            return [];
        }

        foreach ($queries as $type => $query) {
            if (!$query instanceof Fluent) {
                throw new InvalidArgumentException("Query $type is not instance of " . Fluent::class);
            }
            if (($command = $query->getCommand()) !== 'SELECT') {
                throw new InvalidArgumentException("Query $type must be a SELECT, got: " . $command);
            }

            $query->select("'$type' AS type");
        }

        $unionMask = str_repeat('%SQL UNION ', count($queries) - 1) . '%SQL';
        $feedQuery = $this->connection->select('id, instant, type');
        $feedQuery->from("($unionMask) AS feed", ...array_values($queries));

        $feedQuery->orderBy('instant DESC');

        if ($paginator) {
            $feedQuery->offset($paginator->getOffset());
            $feedQuery->limit($paginator->getItemsPerPage());
        }

        $result = $feedQuery->fetchAssoc('id');

        return $result;
    }

    /**
     * @param array $feed
     * @param callable[] $hydrateCallbacks
     *
     * @return FeedEntry[]
     */
    public function hydrateFeed(array $feed, array $hydrateCallbacks): array
    {
        $entities = [];
        foreach ($hydrateCallbacks as $type => $callback) {
            $ids = array_column(array_filter($feed, function ($item) use ($type) {
                return $item['type'] === $type;
            }), 'id');
            $entities[$type] = empty($ids) ? [] : call_user_func($callback, $ids);
        }

        $feedEntities = [];
        foreach ($feed as $entry) {
            $id = $entry['id'];
            $type = $entry['type'];
            $instant = $entry['instant'];

            $feedEntities[$id] = new FeedEntryAdapter($id, $type, $instant, $entities[$entry['type']][$entry['id']]);
        }

        return $feedEntities;
    }
}
