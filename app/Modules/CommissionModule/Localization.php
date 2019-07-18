<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule;


use PAF\Modules\CommissionModule\Model\PafCase;

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
