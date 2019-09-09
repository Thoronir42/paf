<?php declare(strict_types=1);

namespace PAF\Common\Forms;

use Nette\InvalidArgumentException;
use Nette\Localization\ITranslator;
use Nette\SmartObject;

class FormFactory
{
    use SmartObject;

    /** @var ITranslator */
    private $translator;

    public function __construct(ITranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param string $formClass
     *
     * @return Form
     */
    public function create(string $formClass = Form::class): Form
    {
        if (!is_a($formClass, Form::class, true)) {
            throw new InvalidArgumentException("Parameter \$formClass must be a class extending " . Form::class);
        }
        /** @var Form $form */
        $form = new $formClass();
        $form->setTranslator($this->translator);

        $form->setRenderer(new BootstrapFormRenderer());

        return $form;
    }
}
