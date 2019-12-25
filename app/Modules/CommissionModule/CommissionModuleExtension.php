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
        $priceList->setArgument('data', Neon::decode(file_get_contents(__DIR__ . '/../../config/priceList.neon')));
    }
}
