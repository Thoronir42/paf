<?php

call_user_func(function () {
    putenv("DEBUG_MODE=1");
    if (!getenv('CONFIG_FILE')) {
        putenv('CONFIG_FILE=config.testing.neon');
    }

    /** @var \Nette\DI\Container $container */
    $container = include __DIR__ . "/../app/bootstrap.php";

    PAF\Utils\TestUtils::setContainer($container);
    PAF\Utils\TestDBUtils::setLeanConnection($container->getByType(\LeanMapper\Connection::class));

    /** @var \LeanMapper\IMapper $mapper */
    $mapper = $container->getService('leanMapper.mapper');
    PAF\Utils\TestDBUtils::setLeanMapper($mapper);
});
