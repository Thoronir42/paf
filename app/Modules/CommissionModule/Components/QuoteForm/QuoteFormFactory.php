<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\QuoteForm;

use Nette\Localization\ITranslator;
use PAF\Common\Forms\FormFactory;
use PAF\Modules\CommissionModule\Facade\ProductService;
use PAF\Modules\PortfolioModule\Localization;

class QuoteFormFactory
{
    /** @var FormFactory */
    private $formFactory;
    /** @var ITranslator */
    private $translator;
    /**
     * @var ProductService
     */
    private $productService;

    public function __construct(FormFactory $formFactory, ITranslator $translator, ProductService $productService)
    {
        $this->formFactory = $formFactory;
        $this->translator = $translator;
        $this->productService = $productService;
    }

    /** @return QuoteForm */
    public function create()
    {
        /** @var QuoteForm $form */
        $form = $this->formFactory->create(QuoteForm::class);
        $form->initialize($this->productService->getTypes());

        return $form;
    }
}
