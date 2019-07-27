<?php declare(strict_types=1);

namespace PAF\Modules\QuoteModule\Components\QuoteForm;

interface QuoteFormFactory
{
    /** @return QuoteForm */
    public function create();
}
