<?php

define('TEST_RUN', true);

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

    \Test\PAF\Utils\TestDBUtils::initDatabase([
        dirname(__DIR__) . '/extensions/SeStep/LeanSettings/database/drop.sql',
        dirname(__DIR__) . '/extensions/SeStep/LeanSettings/database/initialize.sql',
    ]);

    $mapper = new \SeStep\ModularLeanMapper\ModularMapper(new \PAF\Common\Model\UnderscoreMapper(), [
        'ss_settings__' => new \SeStep\LeanSettings\LeanOptionsMapperModule(),
    ]);

    \Test\PAF\Utils\TestDBUtils::setLeanMapper($mapper);
});


