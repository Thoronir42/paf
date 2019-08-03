<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\QuoteForm;

interface QuoteFormFactory
{
    /** @return QuoteForm */
    public function create();
}
