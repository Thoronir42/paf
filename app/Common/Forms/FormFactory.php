<?php declare(strict_types=1);

namespace PAF\Common\Forms;

use Nette\InvalidArgumentException;
use Nette\Localization\ITranslator;

use Nette\Forms\Form as NetteForm;

class FormFactory
{
    private ITranslator $translator;
    private string $defaultFormClass;

    public function __construct(ITranslator $translator, string $defaultFormClass = NetteForm::class)
    {
        $this->translator = $translator;
        $this->defaultFormClass = $defaultFormClass;
    }

    /**
     * @param ?string $formClass
     *
     * @return NetteForm
     */
    public function create(string $formClass = null): NetteForm
    {
        if (!$formClass) {
            $formClass = $this->defaultFormClass;
        }
        if (!is_a($formClass, NetteForm::class, true)) {
            throw new InvalidArgumentException("Parameter \$formClass must be a class extending " . NetteForm::class);
        }
        /** @var NetteForm $form */
        $form = new $formClass();
        $form->setTranslator($this->translator);

        $form->setRenderer(new BootstrapFormRenderer());

        return $form;
    }
}
