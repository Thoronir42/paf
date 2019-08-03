<?php declare(strict_types=1);

namespace PAF\Utils;

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
        $this->settings->setValue('paf.quotes.enable', true);
        $this->settings->setValue('paf.quotes.preferedSpecies', [
            'puppy',
            'get & killing',
        ]);
        $this->settings->setValue('paf.priceList', [
            'basePrice' => 420,
            'extraFeature' => 69
        ]);
    }
}
