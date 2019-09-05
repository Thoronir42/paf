<?php

namespace Test\PAF\Utils;

use LeanMapper\Connection;
use LeanMapper\IMapper;
use PAF\Commands\InitDatabaseCommand;
use PAF\Common\Model\BaseEntity;

final class TestDBUtils
{
    /** @var Connection */
    private static $leanConnection;

    /** @var IMapper */
    private static $leanMapper;

    public static function getLeanConnection(): Connection
    {
        return self::$leanConnection;
    }

    public static function setLeanConnection(Connection $leanConnection): void
    {
        self::$leanConnection = $leanConnection;
    }

    public static function getLeanMapper(): IMapper
    {
        return self::$leanMapper;
    }

    public static function setLeanMapper(IMapper $leanMapper): void
    {
        self::$leanMapper = $leanMapper;
    }

    public static function truncateEntityTable(string $entityClass)
    {
        $table = self::$leanMapper->getTable($entityClass);
        TestDBUtils::$leanConnection->nativeQuery("TRUNCATE $table;");
    }
}
