<?php declare(strict_types=1);

namespace PAF\Utils;

use LeanMapper\Connection;
use LeanMapper\IMapper;

trait LeanAwareTest
{
    /** @var IMapper */
    private static $leanMapper;
    /** @var Connection */
    private static $leanConnection;

    public static function initializeLeanAware(IMapper $mapper, Connection $connection)
    {
        self::$leanMapper = $mapper;
        self::$leanConnection = $connection;
    }

    private function truncateEntityTable(string $entityClass)
    {
        $table = self::$leanMapper->getTable($entityClass);
        self::$leanConnection->nativeQuery("TRUNCATE $table;");
    }
}
