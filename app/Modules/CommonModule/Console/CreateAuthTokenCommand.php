<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Console;

use PAF\Common\Security\Authenticator;
use PAF\Modules\CommonModule\Services\Users;
use SeStep\Moment\HasMomentProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAuthTokenCommand extends Command
{
    use HasMomentProvider;

    private Users $users;
    private Authenticator $authenticator;

    public function __construct(Users $users, Authenticator $authenticator)
    {
        parent::__construct();
        $this->users = $users;
        $this->authenticator = $authenticator;
    }


    protected function configure()
    {
        $this->addArgument('login', InputArgument::REQUIRED, 'Login of user whose token to generate');

        $this->addOption('duration', 'd', InputOption::VALUE_REQUIRED, "How long should the token last", "+1 day");
        $this->addOption('indefinite', 'i', null, "Clears the duration");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $expireAt = $input->getOption('indefinite') ?
            null : $this->getMomentProvider()->now()->modify($input->getOption('duration'));

        $login = $input->getArgument('login');
        $user = $this->users->findOneByLogin($login);
        if (!$user) {
            $output->write("User with login '$login' does not exist");
            return 1;
        }

        $token = $this->authenticator->createAuthToken($user, $expireAt, 'cli');
        $output->write($token);

        return 0;
    }
}
