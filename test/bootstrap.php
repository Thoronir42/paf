<?php

define('TEST_RUN', true);

$container = include __DIR__ . "/../app/bootstrap.php";

\Test\PAF\Utils\TestUtils::setContainer($container);

