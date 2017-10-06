<?php

namespace App\Common\Helpers;


use App\Common\Model\Entity\Fursuit;
use App\Common\Model\Entity\PafCase;

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

    public static function getCaseStatuses()
    {
        $statuses = [];
        foreach (PafCase::getStatuses() as $status) {
            $statuses[$status] = "paf.case.statuses.$status";
        }

        return $statuses;
    }
}
