<?php

namespace App\Common\Services\Latte;


class ContactUrlFilter extends BaseFilter
{
    private static $FORMATS = [
        'telegram' => 'tg://?to=%s'
    ];


    public function useFilter(...$args)
    {
        return sprintf(self::$FORMATS[$args[1]], $args[0]);
    }
}
