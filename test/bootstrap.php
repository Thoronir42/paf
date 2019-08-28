<?php

define('DEBUG_MODE', true);

call_user_func(function () {
    if(!getenv('ADDITIONAL_CONFIG')) {
        putenv('ADDITIONAL_CONFIG=testing.neon');
    }

    /** @var \Nette\DI\Container $container */
    $container = include __DIR__ . "/../app/bootstrap.php";

    $testingParameters = $container->getParameters()['testing'];

    \Test\PAF\Utils\TestUtils::setContainer($container);
    \Test\PAF\Utils\TestDBUtils::setLeanConnection($container->getByType(\LeanMapper\Connection::class));

    $initCommand = "app:database:init --default-files";
    if (isset($testingParameters['dropAllTables']) && $testingParameters['dropAllTables']) {
        $initCommand .= ' --drop-all-tables';
    }

    /** @var \Contributte\Console\Application $app */
    $app = $container->getByType(\Contributte\Console\Application::class);
    $app->setAutoExit(false);

    $app->run(new \Symfony\Component\Console\Input\StringInput($initCommand));

    /** @var \LeanMapper\IMapper $mapper */
    $mapper = $container->getService('leanMapper.mapper');
    \Test\PAF\Utils\TestDBUtils::setLeanMapper($mapper);

});


