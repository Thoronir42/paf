<?php

call_user_func(function () {
    /** @var \Nette\DI\Container $container */
    $container = require __DIR__ . '/../bootstrap.php';

    /** @var \PAF\Commands\InitDatabaseCommand $initDatabaseCommand */
    $initDatabaseCommand = $container->createInstance(\PAF\Commands\InitDatabaseCommand::class);

    $initDatabaseCommand->setFiles([
        dirname(__DIR__) . '/Modules/CommonModule/Model/database/initialize.sql',
        dirname(__DIR__) . '/Modules/CommissionModule/Model/database/initialize.sql',
        dirname(__DIR__) . '/Modules/PortfolioModule/Model/database/initialize.sql',
        dirname(__DIR__) . '/../extensions/SeStep/LeanSettings/database/initialize.sql',
    ]);

    $initDatabaseCommand->run();
});
