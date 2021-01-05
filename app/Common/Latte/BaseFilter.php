<?php declare(strict_types=1);

namespace PAF\Common\Latte;

use Nette\SmartObject;

abstract class BaseFilter
{
    use SmartObject;

    final public function __invoke(...$args): string
    {
        return call_user_func_array([$this, 'useFilter'], $args);
    }

    abstract public function useFilter(...$args): string;
}
