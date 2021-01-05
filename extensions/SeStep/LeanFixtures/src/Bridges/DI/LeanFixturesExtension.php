<?php declare(strict_types=1);

namespace SeStep\LeanFixtures\Bridges\DI;

use Nette;
use Nette\DI\CompilerExtension;
use SeStep\LeanFixtures\EntityFixtures;
use SeStep\LeanFixtures\InitFixturesCommand;
use Symfony\Component\Console\Command\Command;

class LeanFixturesExtension extends CompilerExtension
{
    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::structure([
            'initFiles' => Nette\Schema\Expect::arrayOf(Nette\Schema\Expect::string()),
            'daos' => Nette\Schema\Expect::array(),
        ]);
    }


    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();


        $builder->addDefinition($this->prefix('entityFixtures'))
            ->setType(EntityFixtures::class)
            ->setArgument('daos', $config->daos);

        if (class_exists(Command::class)) {
            $files = $config->initFiles;
            $builder->addDefinition($this->prefix('initCommand'))
                ->setType(InitFixturesCommand::class)
                ->setArguments(['files' => $files])
                ->addSetup('setName', ['leanFixtures:initialize'])
                ->addTag('console.command', 'leanFixtures:initialize');
        }
    }
}
