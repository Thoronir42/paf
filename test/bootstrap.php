<?php

define('DEBUG_MODE', true);

call_user_func(function () {
    define('ADDITIONAL_CONFIG', 'testing.neon');

    /** @var \Nette\DI\Container $container */
    $container = include __DIR__ . "/../app/bootstrap.php";

    $testingParameters = $container->getParameters()['testing'];

    \Test\PAF\Utils\TestUtils::setContainer($container);
    \Test\PAF\Utils\TestDBUtils::setLeanConnection($container->getByType(\LeanMapper\Connection::class));


    $command = new \PAF\Commands\InitDatabaseCommand(\Test\PAF\Utils\TestDBUtils::getLeanConnection());
    $command->setFiles([
        dirname(__DIR__) . '/extensions/SeStep/LeanSettings/database/drop.sql',
        dirname(__DIR__) . '/extensions/SeStep/LeanSettings/database/initialize.sql',
    ]);

    $command->run();

    /** @var \LeanMapper\IMapper $mapper */
    $mapper = $container->getService('leanMapper.mapper');
    \Test\PAF\Utils\TestDBUtils::setLeanMapper($mapper);

});


