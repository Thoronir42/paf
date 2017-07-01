<?php

namespace App\Common\Services\Latte;

use Nette\SmartObject;


/**
 * fiksme-possible enhancement: add support for multiple parameters
 * Class BaseFilter
 * @package Libs\LatteFilters
 */
abstract class BaseFilter
{
    use SmartObject;

    public function __invoke($value){
        return $this->useFilter($value);
    }

    public abstract function useFilter($value);
}
