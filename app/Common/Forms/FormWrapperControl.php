<?php declare(strict_types=1);

namespace PAF\Common\Forms;

use Nette\Application\UI as UI;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;

abstract class FormWrapperControl extends UI\Control
{
    /** @var FormFactory */
    protected $factory;
    /** @var ITranslator */
    protected $translator;

    public function __construct(FormFactory $factory, ITranslator $translator)
    {
        $this->factory = $factory;
        $this->translator = $translator;
    }

    /**
     * @return mixed
     */
    abstract public function createComponentForm();

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    abstract public function processForm(Form $form, $values);

    /** @return Form */
    protected function form()
    {
        return $this['form'];
    }
}
