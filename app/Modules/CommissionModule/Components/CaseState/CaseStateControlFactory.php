<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CaseState;

use PAF\Modules\CommissionModule\Model\PafCase;

interface CaseStateControlFactory
{
    public function create(PafCase $case): CaseStateControl;
}
