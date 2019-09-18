<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\QuoteForm;

use Nette\Localization\ITranslator;
use PAF\Common\Forms\FormFactory;
use PAF\Modules\PortfolioModule\Localization;

class QuoteFormFactory
{
    /** @var FormFactory */
    private $formFactory;
    /** @var ITranslator */
    private $translator;

    public function __construct(FormFactory $formFactory, ITranslator $translator)
    {
        $this->formFactory = $formFactory;
        $this->translator = $translator;
    }

    /** @return QuoteForm */
    public function create() {
        /** @var QuoteForm $form */
        $form = $this->formFactory->create(QuoteForm::class);
        $form->initialize(Localization::getFursuitTypes());

        return $form;
    }
}
