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

    public function __construct(Settings $settings)
    {
        parent::__construct();
        $this->settings = $settings;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->settings->setValue('commission.quotes.enable', true);
        $this->settings->setValue('commission.quotes.preferredSpecies', [
            'puppy',
            'get & killing',
        ]);
        $this->settings->setValue('commission.priceList', [
            'basePrice' => 420,
            'extraFeature' => 69
        ]);
    }
}
