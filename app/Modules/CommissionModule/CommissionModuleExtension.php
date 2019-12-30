<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Neon\Neon;

class CommissionModuleExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $this->loadDefinitionsFromConfig($this->loadFromFile(__DIR__ . '/commissionModule.neon')['services']);

        /** @var ServiceDefinition $priceList */
        $priceList = $this->getContainerBuilder()->getDefinition($this->prefix('priceList'));
        $priceListNeon = __DIR__ . '/../../config/priceList.neon';
        $priceList->setArgument('data', Neon::decode(file_get_contents($priceListNeon)));

        $this->compiler->addDependencies([$priceListNeon]);
    }
}
