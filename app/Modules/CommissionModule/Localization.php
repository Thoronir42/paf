<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule;

use PAF\Modules\CommissionModule\Model\PafCaseWorkflow;

class Localization
{
    public static function getCaseStatuses()
    {
        $statuses = [];
        foreach (PafCaseWorkflow::getStates() as $status) {
            $statuses[$status] = "paf.case.statuses.$status";
        }

        return $statuses;
    }
}
