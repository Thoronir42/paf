<?php

namespace App\Common\Services\Latte;

/**
 * fiksme-possible enhancement: multi-lingual filter support
 * Class YesNoFilter
 * @package Libs\LatteFilters
 */
class YesNoFilter extends BaseFilter
{
    /**
     * @param bool $args
     * @return string
     */
    public function useFilter(...$args)
    {
        if (!$args || !is_array($args) || sizeof($args) < 1 || !$args[0]) {
            return 'No';
        }
        return 'Yes';
    }
}
