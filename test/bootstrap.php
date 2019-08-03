<?php

define('DEBUG_MODE', true);

call_user_func(function () {
    $container = include __DIR__ . "/../app/bootstrap.php";

    \Test\PAF\Utils\TestUtils::setContainer($container);

    \Test\PAF\Utils\TestDBUtils::setLeanConnection(new \LeanMapper\Connection([
        'host' => 'localhost',
        'driver' => 'mysqli',
        'user' => 'tester',
        'password' => 'QjpebVnpAKBQaKMr',
        'database' => 'paf_test',
    ]));

    $command = new \PAF\Commands\InitDatabaseCommand(\Test\PAF\Utils\TestDBUtils::getLeanConnection());
    $command->setFiles([
        dirname(__DIR__) . '/extensions/SeStep/LeanSettings/database/drop.sql',
        dirname(__DIR__) . '/extensions/SeStep/LeanSettings/database/initialize.sql',
    ]);

    $command->run();

    $mapper = new \SeStep\ModularLeanMapper\ModularMapper(new \PAF\Common\Model\UnderscoreMapper(), [
        'ss_settings__' => new \SeStep\LeanSettings\LeanOptionsMapperModule(),
    ]);

    \Test\PAF\Utils\TestDBUtils::setLeanMapper($mapper);
});


