<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Presenters;

use PAF\Common\BasePresenter;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use PAF\Modules\CommonModule\Components\SignInForm\SignInFormFactory;

class SignPresenter extends BasePresenter
{
    /** @var SignInFormFactory @inject */
    public $in_factory;

    public function actionDefault()
    {
        $this->redirect('in');
    }

    public function actionIn()
    {
    }

    public function actionOut()
    {
        $this->getUser()->logout(true);
        $this->redirect('Default:');
    }

    protected function createComponentSignInForm()
    {
        $form = $this->in_factory->create();

        $form->onSave[] = function (Form $form, $values) {
            $login = $values->login;
            $password = $values->password;
            $remember = $values->remember;

            if ($remember) {
                $this->user->setExpiration('14 days', false);
            } else {
                $this->user->setExpiration('30 minutes', true);
            }

            try {
                $this->user->login($login, $password);
            } catch (AuthenticationException $e) {
                $form->addError('Nesprávné jméno nebo heslo.');

                return;
            }
            $this->redirect('Default:');
        };

        return $form;
    }

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignUpForm()
    {
        // fixme: move creation into facade
        $form = $this->up_factory->create();

        $form->onSave[] = function (Form $form, $values) {
            $login = $values->login;
            $password = $values->password;

            try {
                $user = $this->users->create($login, $password);
                $this->users->persist($user);
            } catch (Nette\InvalidStateException $ex) {
                $form->addError('Uživatel se jménem' . $login . ' již existuje.');

                return;
            }

            $this->flashMessage('Váš účet byl vytvořen.');

            $this->redirect('in');
        };

        return $form;
    }
}
