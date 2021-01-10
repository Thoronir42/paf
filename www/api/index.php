<?php declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

$container = PAF\Bootstrap::createContainer('config.api.neon');
$application = $container->getByType(Nette\Application\Application::class);
$application->run();
