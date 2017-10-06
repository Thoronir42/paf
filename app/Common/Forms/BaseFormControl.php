<?php

namespace App\Common\Forms;

use Kdyby\Translation\Translator;
use Nette\Application\UI as UI;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;


/**
 * Class BaseFormControl
 * @package App\Forms
 */
abstract class BaseFormControl extends UI\Control
{
    /** @var callable[]  function (Form $form, ArrayHash $result); Occurs when form successfully validates input. */
    public $onSave;

    /** @var FormFactory */
    protected $factory;
    /** @var Translator */
    protected $translator;

    public function __construct(FormFactory $factory, Translator $translator)
    {
        parent::__construct();
        $this->factory = $factory;
        $this->translator = $translator;
    }

    /**
     * @return mixed
     */
    public abstract function createComponentForm();

    /**
     * @param Form      $form
     * @param ArrayHash $values
     */
    public abstract function processForm(Form $form, $values);

    /** @return Form */
    protected function form()
    {
        return $this['form'];
    }
}
