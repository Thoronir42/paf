<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Components\SignInForm;


use Nette\Application\UI\Form;
use PAF\Common\Forms\FormWrapperControl;

/**
 * Class SignInForm
 * @package PAF\Modules\CommonModule\Components\SignInForm
 *
 * @method onSave($form, $values)
 */
class SignInForm extends FormWrapperControl
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
