<?php

namespace Test\PAF\Utils;


use LeanMapper\Connection;
use LeanMapper\IMapper;

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

    public static function initDatabase(array $scriptsPaths = [])
    {
        echo "Initializing database ... \n";
        foreach ($scriptsPaths as $path) {
            echo "  - $path ...";
            if(!file_exists($path)) {
                echo " file not found\n";
                return;
            }

            $script = file_get_contents($path);
            try {
                self::$leanConnection->nativeQuery($script);

                echo " ok \n";
            } catch (\Throwable $ex) {
                echo " error \n";
                throw $ex;
            }
        }
    }


}