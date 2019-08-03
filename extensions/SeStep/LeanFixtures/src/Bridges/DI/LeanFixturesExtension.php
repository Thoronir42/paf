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
        ])->castTo('array');
    }


    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('entityFixtures'))
            ->setType(EntityFixtures::class);

        if (class_exists(Command::class)) {
            $files = $this->getConfig()['initFiles'];
            $builder->addDefinition($this->prefix('initCommand'))
                ->setType(InitFixturesCommand::class)
                ->setArguments(['files' => $files]);
        }
    }
}
