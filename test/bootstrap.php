<?php

define('DEBUG_MODE', true);

call_user_func(function () {
    if(!getenv('CONFIG_FILE')) {
        putenv('CONFIG_FILE=config.testing.neon');
    }

    /** @var \Nette\DI\Container $container */
    $container = include __DIR__ . "/../app/bootstrap.php";

    \Test\PAF\Utils\TestUtils::setContainer($container);
    \Test\PAF\Utils\TestDBUtils::setLeanConnection($container->getByType(\LeanMapper\Connection::class));

    /** @var \LeanMapper\IMapper $mapper */
    $mapper = $container->getService('leanMapper.mapper');
    \Test\PAF\Utils\TestDBUtils::setLeanMapper($mapper);

});


