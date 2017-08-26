<?php

namespace App\Common\Controls\Forms\SignInForm;

use App\Common\Controls\Forms\BaseFormControl;
use Nette\Application\UI\Form;

class SignInForm extends BaseFormControl
{
    public function render()
    {
        $this->template->setFile(__DIR__ . '/signInForm.latte');
        $this->template->render();
    }

    public function createComponentForm()
    {
        $form = $this->factory->create();
        $form->setTranslator($this->translator);

        $form->addText('login', 'generic.login')
            ->setRequired(true);

        $form->addPassword('password', 'generic.password')
            ->setRequired();

        $form->addCheckbox('remember', 'generic.remember-me');

        $form->addSubmit('send', 'generic.submit');

        $form->onSuccess[] = [$this, 'processForm'];

        return $form;
    }


    public function processForm(Form $form, $values)
    {
        $this->onSave($form, $values);
    }
}

interface ISignInFormFactory
{
    /** @return SignInForm */
    function create();
}
