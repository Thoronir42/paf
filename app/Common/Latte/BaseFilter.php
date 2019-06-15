<?php declare(strict_types=1);

namespace PAF\Common\Latte;

use Nette\SmartObject;


/**
 * Class BaseFilter
 * @package Libs\LatteFilters
 */
abstract class BaseFilter
{
    use SmartObject;

    public final function __invoke(...$args): string
    {
        return call_user_func_array([$this, 'useFilter'], $args);
    }

    public abstract function useFilter(...$args): string;
}
