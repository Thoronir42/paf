<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\PafCaseForm;

interface PafCaseFormFactory
{
    /**
     * @return PafCaseForm
     */
    public function create();
}
