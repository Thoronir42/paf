<?php declare(strict_types=1);

namespace PAFData\Fixtures\Modules\CommonModule;


use LeanMapper\IMapper;
use PAF\Modules\CommonModule\Repository\UserRepository;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class CommonEntityCreator
{
    /** @var UserRepository */
    protected $users;

    /** @var OutputInterface */
    protected $output;

    public function __construct(UserRepository $userRepository, IMapper $mapper)
    {
        $this->users = $userRepository;
        $this->setOutput();
    }

    /** @param OutputInterface $output */
    public function setOutput(OutputInterface $output = null): void
    {
        $this->output = $output ?: new NullOutput();
    }

    public function user($username, $password)
    {
        $result = $this->users->findOneByUsername($username);
        if ($result) {
            $this->output->writeln("Err- User $username already exist.", Output::VERBOSITY_VERBOSE);

            return $result;
        } else {
            $user = $this->users->create($username, $password);
            $this->users->persist($user);

            $this->output->writeln("Ok - User $username has been created.");

            return $user;
        }
    }
}
