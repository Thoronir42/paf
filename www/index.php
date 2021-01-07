<?php declare(strict_types=1);

// Uncomment this line if you must temporarily take down your site for maintenance.
// require __DIR__ . '/.maintenance.php';

require __DIR__ . '/../vendor/autoload.php';

$container = PAF\Bootstrap::createContainer('include/appRouting.neon');
$application = $container->getByType(Nette\Application\Application::class);
$application->run();
