<?php

namespace App\Common\Helpers;


use App\Common\Model\Entity\Fursuit;

class LocalizationHelper
{
    public static function getFursuitTypes()
    {
        $types = [];
        foreach (Fursuit::getTypes() as $type) {
            $types[$type] = "paf.fursuit.types.$type";
        }

        return $types;
    }
}
