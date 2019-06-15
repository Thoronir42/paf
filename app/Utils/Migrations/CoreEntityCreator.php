<?php declare(strict_types=1);

namespace PAF\Utils\Migrations;

use PAF\Common\Model\Embeddable\Contact;
use PAF\Common\Model\Embeddable\FursuitSpecification;
use PAF\Common\Model\Entity\Quote;
use PAF\Common\Services\Doctrine\PafEntities;
use PAF\Common\Services\Doctrine\QuoteRepository;
use PAF\Common\Services\Doctrine\Users;
use SeStep\FileAttachable\Service\Files;
use SeStep\Migrations\IServiceProvider;
use Symfony\Component\Console\Output\OutputInterface;

class CoreEntityCreator
{
    /** @var Users */
    protected $users;

    /** @var PafEntities */
    protected $pafEntities;

    /** @var QuoteRepository */
    protected $quotes;

    /** @var Files */
    protected $files;

    /** @var OutputInterface */
    protected $output;

    public function __construct(IServiceProvider $provider, OutputInterface $output)
    {
        $this->files = $provider->getService(Files::class);
        $this->users = $provider->getService(Users::class);
        $this->quotes = $provider->getService(QuoteRepository::class);
        $this->pafEntities = $provider->getService(PafEntities::class);

        $this->output = $output;
    }

    public function user($username, $password)
    {
        $result = $this->users->findOneByUsername($username);
        if ($result) {
            $this->output->writeln("Err- User $username already exist.");

            return $result;
        } else {
            $user = $this->users->create($username, $password);
            $this->users->save($user);

            $this->output->writeln("Ok - User $username has been created.");

            return $user;
        }
    }

    public function quote(Contact $contact, FursuitSpecification $fursuit, $files = [])
    {
        $quote = new Quote($contact, $fursuit);
        $quote->setReferences($this->files->createThread(true));

        $name = $fursuit->getName();

        if (!$this->pafEntities->createQuote($quote)) {
            $this->output->writeln("Err- Quote {$name} already exists.");
        } else {
            $this->output->writeln("Ok - Quote {$name} created successfully.");
        }

        return $quote;
    }
}
