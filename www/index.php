<?php declare(strict_types=1);

// Uncomment this line if you must temporarily take down your site for maintenance.
// require __DIR__ . '/.maintenance.php';

/** @var Nette\DI\Container $container */
$container = require __DIR__ . '/../app/bootstrap.php';

/** @var Nette\Application\Application $application */
$application = $container->getByType(Nette\Application\Application::class);
$application->run();
