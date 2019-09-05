<?php declare(strict_types=1);
/**
 * Bootstrap file of the application. Used to:
 *   - configure debugger and debug environment enabling
 *   - prepare configuration files
 *   - create and return DI\Container
 */

/**
 * That autoload
 */
require __DIR__ . '/../vendor/autoload.php';

return call_user_func(static function () {
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
    ]);

    $configurator->enableDebugger(dirname(__DIR__) . '/log');
    $configurator->setTempDirectory(dirname(__DIR__) . '/temp');

    $configurator->addConfig(__DIR__ . '/config/config.neon');

    $configFile = getenv('CONFIG_FILE') ?: 'config.local.neon';
    $configurator->addConfig(__DIR__ . '/config/' . $configFile);

    PAF\Common\Forms\FormFactory::adjustValidatorMessages();

    $container = $configurator->createContainer();

    return $container;
});
