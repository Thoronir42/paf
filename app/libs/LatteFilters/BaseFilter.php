<?php

namespace Libs\LatteFilters;


use Nette\Object;

/**
 * fiksme-possible enhancement: add support for multiple parameters
 * Class BaseFilter
 * @package Libs\LatteFilters
 */
abstract class BaseFilter extends Object
{
    public function __invoke($value){
        return $this->useFilter($value);
    }

    public abstract function useFilter($value);
}