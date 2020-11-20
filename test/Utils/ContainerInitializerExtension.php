<?php declare(strict_types=1);

namespace PAF\Utils;

use Data\InitDatabaseCommand;
use Nette\DI\Container;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class ContainerInitializerExtension extends InitializerExtension
{
    /** @var Container */
    private $container;

    /** @var string[] */
    private $initializerFunctions;

    public function __construct(array $initializerFunctions = [])
    {
        $this->initializerFunctions = $initializerFunctions;
    }

    public function initializeClass(string $className): void
    {
        // TODO: Come up with more granular approach to database initialization
        if (in_array(LeanAwareTest::class, class_uses($className))) {
            $this->initializeDbStructure();
        }

        foreach ($this->initializerFunctions as $function) {
            if (!is_callable([$className, $function])) {
                continue;
            }

            $functionName = "$className::$function";
            if ($this->isInitialized($functionName)) {
                continue;
            }

            $this->getContainer()->callMethod([$className, $function]);
            $this->markInitialized($functionName);
        }
        $this->markInitialized($className);
    }

    private function getContainer(): Container
    {
        if (!$this->container) {
            $this->container = include __DIR__ . "/../../app/bootstrap.php";
        }

        return $this->container;
    }

    /**
     * Initializes whole db structure
     *
     * @deprecated a quick-fix, desired solution is to only initialize structure
     *   of tested functionality
     */
    private function initializeDbStructure()
    {
        if ($this->isInitialized('_db_structure')) {
            return;
        }

        $initCommand = $this->getContainer()->getByType(InitDatabaseCommand::class);
        $input = new ArgvInput(['paf-test', '-d', '--drop-all-tables'], $initCommand->getDefinition());
        $initCommand->run($input, new ConsoleOutput());
        $this->markInitialized('_db_structure');
    }
}
