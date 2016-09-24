<?php

require __DIR__ . '/../vendor/autoload.php';

\Tracy\Debugger::$showLocation = true;
\Tracy\Debugger::$maxDepth = 4;

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');


$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
if (!isset($_SERVER['REMOTE_ADDR']) OR in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) { // !isset is for doctrine
	$configurator->addConfig(__DIR__ . '/config/config.local.neon');
} else {
	$configurator->addConfig(__DIR__ . '/config/config.production.neon');
}

$container = $configurator->createContainer();

return $container;
