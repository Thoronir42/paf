<?php

namespace App\Controls\Forms\SignInForm;

use App\Controls\Forms\BaseFormControl;
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

        $form->addText('login', 'Who?')
            ->setRequired();

        $form->addPassword('password', 'U ? ? ???')
            ->setRequired();

        $form->addCheckbox('remember', 'Pls, remember');

        $form->addSubmit('send', 'Whrrr?!');

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
