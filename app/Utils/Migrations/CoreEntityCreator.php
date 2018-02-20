<?php

namespace App\Utils\Migrations;

use App\Common\Model\Embeddable\Contact;
use App\Common\Model\Embeddable\FursuitSpecification;
use App\Common\Model\Entity\Quote;
use App\Common\Services\Doctrine\PafEntities;
use App\Common\Services\Doctrine\Quotes;
use App\Common\Services\Doctrine\Users;
use SeStep\FileAttachable\Service\Files;
use SeStep\Migrations\IServiceProvider;
use Symfony\Component\Console\Output\OutputInterface;

class CoreEntityCreator
{
    /** @var Users */
    protected $users;

    /** @var PafEntities */
    protected $pafEntities;

    /** @var Quotes */
    protected $quotes;

    /** @var Files */
    protected $files;

    /** @var OutputInterface */
    protected $output;

    public function __construct(IServiceProvider $provider, OutputInterface $output)
    {
        $this->files = $provider->getService(Files::class);
        $this->users = $provider->getService(Users::class);
        $this->quotes = $provider->getService(Quotes::class);
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
