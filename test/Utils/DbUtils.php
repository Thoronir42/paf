<?php declare(strict_types=1);

namespace PAF\Utils;

use PAF\Common\Console\InitDatabaseCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class DbUtils
{
    private static bool $initialized = false;

    /**
     * Initializes whole db structure
     *
     * @param InitDatabaseCommand $initCommand
     * @param string $className
     */
    public static function initializeDbStructure(InitDatabaseCommand $initCommand, string $className)
    {
        if (self::$initialized || !in_array(LeanAwareTest::class, class_uses($className))) {
            return;
        }

        // TODO: Tweak initialization to be more granular instead of -d option
        $input = new ArgvInput(['paf-test', '-d', '--drop-all-tables'], $initCommand->getDefinition());
        $initCommand->run($input, new ConsoleOutput());

        self::$initialized = true;
    }
}
