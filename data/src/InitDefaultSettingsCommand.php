<?php declare(strict_types=1);

namespace Data;

use SeStep\GeneralSettings\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitDefaultSettingsCommand extends Command
{
    protected static $defaultName = 'app:settings:setDefaults';

    /** @var Settings */
    private $settings;

    /** @var array */
    private $defaultSettings;

    /**
     * InitDefaultSettingsCommand constructor.
     * @param Settings $settings
     * @param array $defaultSettings associative array of settings to be initialized
     */
    public function __construct(Settings $settings, array $defaultSettings)
    {
        parent::__construct();
        $this->settings = $settings;
        $this->defaultSettings = $defaultSettings;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($this->defaultSettings)) {
            $output->writeln("Nothing to initialize");
            return;
        }

        foreach ($this->defaultSettings as $name => $value) {
            $this->settings->setValue($name, $value);
            $output->writeln("Initialized value for $name", $output::VERBOSITY_VERBOSE);
        }

        $count = count($this->defaultSettings);
        $output->writeln("Initialized $count settings fields");
    }
}
