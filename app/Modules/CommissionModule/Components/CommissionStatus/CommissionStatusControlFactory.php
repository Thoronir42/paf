<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CommissionStatus;

use PAF\Modules\CommissionModule\Model\Commission;

interface CommissionStatusControlFactory
{
    public function create(Commission $commission): CommissionStatusControl;
}
