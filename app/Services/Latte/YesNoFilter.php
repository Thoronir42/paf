<?php

namespace App\Services\Latte;

/**
 * fiksme-possible enhancement: multi-lingual filter support
 * Class YesNoFilter
 * @package Libs\LatteFilters
 */
class YesNoFilter extends BaseFilter
{
    /**
     * @param bool $value
     * @return string
     */
    public function useFilter($value)
    {
        return $value ? 'Yes' : 'No';
    }
}
