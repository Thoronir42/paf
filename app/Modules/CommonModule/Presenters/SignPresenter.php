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
    public $signInFormFactory;

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
        $form = $this->signInFormFactory->create();

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
                $form->addError('generic.sign.invalidLoginAttempt', false);
            }
        };

        return $form;
    }
}
