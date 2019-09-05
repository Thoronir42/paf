<?php declare(strict_types=1);

namespace Test\PAF\Utils;

use Nette\DI\Container;

final class TestUtils
{
    /** @var Container */
    private static $container;

    public static function setContainer(Container $container)
    {
        self::$container = $container;
    }

    public static function getContainer(): Container
    {
        return self::$container;
    }
}
