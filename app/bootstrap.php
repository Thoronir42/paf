<?php

require __DIR__ . '/../vendor/autoload.php';
(new \App\Libs\FactoryInterfaceAutoloader())->register();

\Tracy\Debugger::$showLocation = true;
\Tracy\Debugger::$maxDepth = 4;

$configurator = new Nette\Configurator();

$debugList = [
    'whoop whoop',
    'nobody watches over me',
];

$configurator->setDebugMode($debugList); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');


$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(__DIR__ . '/config/config.neon');
if (!isset($_SERVER['REMOTE_ADDR']) || in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) { // !isset is for doctrine
	$configurator->addConfig(__DIR__ . '/config/config.local.neon');
} else {
	$configurator->addConfig(__DIR__ . '/config/config.production.neon');
}

$container = $configurator->createContainer();

\App\Common\Forms\FormFactory::adjustValidatorMessages();

return $container;
