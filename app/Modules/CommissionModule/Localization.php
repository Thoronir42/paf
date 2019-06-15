<?php declare(strict_types=1);

namespace PAF\Common\Helpers;


use PAF\Common\Model\Entity\Fursuit;
use PAF\Common\Model\Entity\PafCase;

class Localization
{

    public static function getCaseStatuses()
    {
        $statuses = [];
        foreach (PafCase::getStatuses() as $status) {
            $statuses[$status] = "paf.case.statuses.$status";
        }

        return $statuses;
    }
}
