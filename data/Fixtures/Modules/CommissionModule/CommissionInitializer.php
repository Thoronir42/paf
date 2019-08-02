<?php declare(strict_types=1);

namespace PAFData\Fixtures\Modules\CommissionModule;

use PAFData\Fixtures\InitializerModule;
use SeStep\GeneralSettings\Settings;
use Tracy\Debugger;

final class CommissionInitializer extends InitializerModule
{
    /** @var Settings */
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function run(): int
    {
        $this->setOptions();

        return 0;
    }


    private function setOptions()
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
