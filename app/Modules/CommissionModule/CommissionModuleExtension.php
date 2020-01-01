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
        $builder = $this->getContainerBuilder();

        $priceList = $builder->getDefinition($this->prefix('priceList'));
        $priceListNeon = __DIR__ . '/../../config/priceList.neon';
        $priceList->setArgument('data', Neon::decode(file_get_contents($priceListNeon)));

        $commissionService = $builder->getDefinition($this->prefix('commissionService'));

        /** @var ServiceDefinition $quoteService */
        $quoteService = $builder->getDefinition($this->prefix('quoteService'));
        $quoteService->addSetup('$service->onQuoteAccept[] = function($quote) { 
        $commissionService = ?; 
        $commissionService->createFromQuote($quote);
        }', [$commissionService]);

        $this->compiler->addDependencies([$priceListNeon]);
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $commissionService = $builder->getDefinition($this->prefix('commissionService'));
        $quoteService = $builder->getDefinition($this->prefix('quoteService'));

        /** @var ServiceDefinition $dashboardStats */
        $dashboardStats = $builder->getDefinition('common.dashboardService');
        $dashboardStats->addSetup(
            '$service->registerStat(?, [?, "countUnresolvedQuotes"])',
            ['quotes', $quoteService]
        );
        $dashboardStats->addSetup(
            '$service->registerStat(?, [?, "countUnresolvedCommissions"])',
            ['commissions', $commissionService]
        );
    }
}
