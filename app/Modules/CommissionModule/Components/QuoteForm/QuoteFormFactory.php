<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Components\QuoteForm;

use Nette\Forms\Controls\SelectBox;
use Nette\Localization\ITranslator;
use PAF\Common\Forms\FormFactory;
use PAF\Modules\CommissionModule\Facade\ProductService;

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
        $fursuitTypes = $this->productService->getTypes();
        $form->initialize($fursuitTypes);

        /** @var SelectBox $fursuitType */
        $fursuitType = $form['fursuit']['type'];
        $disabledTypes = array_diff(array_keys($fursuitTypes), $this->productService->getEnabledTypes());
        $fursuitType->setDisabled($disabledTypes);

        return $form;
    }
}
