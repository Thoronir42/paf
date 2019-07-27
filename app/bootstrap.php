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
    (new PAF\FactoryInterfaceAutoloader())->register();

    Tracy\Debugger::$showLocation = true;
    Tracy\Debugger::$maxDepth = 4;

    $configurator = new Nette\Configurator();

    $debugList = [
        'whoop whoop',
        'nobody watches over me',
    ];

    $debugMode = defined('TEST_RUN') ? true : $debugList;
    $configurator->setDebugMode($debugMode);
    $configurator->enableDebugger(__DIR__ . '/../log');


    $configurator->setTempDirectory(__DIR__ . '/../temp');

    $configurator->addConfig(__DIR__ . '/config/config.neon');
    if (!isset($_SERVER['REMOTE_ADDR']) || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
        $configurator->addConfig(__DIR__ . '/config/config.local.neon');
    } else {
        $configurator->addConfig(__DIR__ . '/config/config.production.neon');
    }

    PAF\Common\Forms\FormFactory::adjustValidatorMessages();

    $container = $configurator->createContainer();

    return $container;
});
