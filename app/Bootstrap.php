<?php declare(strict_types=1);

namespace PAF;

use Nette;
use Tracy;

class Bootstrap
{

    public static function createContainer(): Nette\DI\Container
    {
        Tracy\Debugger::$showLocation = true;
        Tracy\Debugger::$maxDepth = 4;

        $configurator = new Nette\Configurator();

        if (getenv('DEBUG_MODE')) {
            $configurator->setDebugMode(true);
        } else {
            $debugList = [
                'whoop whoop',
                'nobody watches over me',
            ];

            $configurator->setDebugMode($debugList);
        }

        $configurator->addParameters([
            'rootDir' => dirname(__DIR__),
            'modulesDir' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'appModules',
        ]);

        $configurator->enableDebugger(dirname(__DIR__) . '/log');
        $configurator->setTempDirectory(dirname(__DIR__) . '/temp');

        $configurator->addConfig(__DIR__ . '/config/config.neon');

        $configFile = getenv('CONFIG_FILE') ?: 'config.local.neon';
        $configurator->addConfig(__DIR__ . '/config/' . $configFile);

        $container = $configurator->createContainer();

        return $container;
    }

}
