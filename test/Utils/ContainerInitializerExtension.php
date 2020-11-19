<?php declare(strict_types=1);

namespace PAF\Utils;

use Nette\DI\Container;

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
}
