<?php

namespace App\Common\Services\Latte;

use Nette\SmartObject;


/**
 * Class BaseFilter
 * @package Libs\LatteFilters
 */
abstract class BaseFilter
{
    use SmartObject;

    public function __invoke(...$args)
    {
        return call_user_func_array([$this, 'useFilter'], $args);
    }

    public abstract function useFilter(...$args);
}
