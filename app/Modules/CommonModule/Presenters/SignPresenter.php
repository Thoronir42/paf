<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Presenters;

use PAF\Common\BasePresenter;
use Nette;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;
use PAF\Modules\CommonModule\Components\SignInForm\SignInFormFactory;
use PAF\Modules\CommonModule\Repository\UserRepository;

class SignPresenter extends BasePresenter
{
    /** @var UserRepository @inject */
    public $users;

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
        $this->redirect('Homepage:');
    }

    protected function createComponentSignInForm()
    {
        $form = $this->in_factory->create();

        $form->onSave[] = function (Form $form, $values) {
            $login = $values->login;
            $password = $values->password;
            $remember = $values->remember;

            if ($remember) {
                $this->user->setExpiration('14 days');
            } else {
                $this->user->setExpiration('30 minutes', Nette\Security\IUserStorage::CLEAR_IDENTITY);
            }

            try {
                $this->user->login($login, $password);

                $this->redirect('Homepage:');
            } catch (AuthenticationException $e) {
                $form->addError($this->translator->translate('authentication.invalid-attempt'));
            }
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
