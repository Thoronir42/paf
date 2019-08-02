<?php


namespace PAFData\Fixtures;


use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitializeFixturesCommand extends Command
{
    protected static $defaultName = 'app:data:initFixtures';

    /** @var InitializerModule[] */
    private $modules = [];
    /** @var Container */
    private $container;

    /**
     * InitializeFixturesCommand constructor.
     * @param Container $container
     * @param InitializerModule[] $modules
     */
    public function __construct(Container $container, array $modules)
    {
        parent::__construct();
        $this->container = $container;
        $this->modules = $modules;
    }

    protected function configure()
    {
        $this->setDescription("Initializes modules with fixture data");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($this->modules)) {
            $output->writeln("I have nothing...");
            return 0;
        }

        foreach ($this->modules as $module) {
            $moduleName = get_class($module);
            $module->setOutput($output);

            $output->writeln("Initializing $moduleName ...");
            $result = $module->run();

            if ($result) {
                $output->writeln("Module $moduleName initialization ended with code $result");
            } else {
                $output->writeln("Module $moduleName initialized successfully.");
            }
        }

        return 0;
    }
}
