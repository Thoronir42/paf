<?php declare(strict_types=1);

namespace SeStep\LeanFixtures;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InitFixturesCommand extends Command
{
    private EntityFixtures $fixtures;

    protected array $files;

    public function __construct(EntityFixtures $fixtures, array $files = [])
    {
        parent::__construct();
        $this->fixtures = $fixtures;
        $this->files = $files;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->files as $file) {
            $loader = new Loaders\NeonFixtureLoader($file);
            $this->fixtures->loadData($loader, $output);
        }
    }
}
