<?php declare(strict_types=1);

namespace PAF\Common\Forms;

use Nette\Application\UI as UI;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;

abstract class FormWrapperControl extends UI\Control
{
    protected FormFactory $factory;
    protected ITranslator $translator;

    public function __construct(FormFactory $factory, ITranslator $translator)
    {
        $this->factory = $factory;
        $this->translator = $translator;
    }

    abstract public function createComponentForm();

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    abstract public function processForm(Form $form, $values);

    protected function form(): Form
    {
        /** @var Form $form */
        $form = $this['form'];
        return $form;
    }
}
