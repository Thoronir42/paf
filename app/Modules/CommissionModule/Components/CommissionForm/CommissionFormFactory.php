<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\CommissionForm;

interface CommissionFormFactory
{
    public function create(): CommissionForm;
}
