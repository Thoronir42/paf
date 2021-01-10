<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule;

use Nette;
use Nette\Schema\Expect;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Neon\Neon;
use PAF\Modules\CommissionModule\Components\QuoteForm\QuoteFormFactory;

class CommissionModuleExtension extends CompilerExtension
{
    const MODE_SINGLE_SUPPLIER = 'singleSupplier';
    const MODE_MULTI_SUPPLIERS = 'multipleSuppliers';

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Expect::structure([
            'mode' => Expect::anyOf(self::MODE_SINGLE_SUPPLIER, self::MODE_MULTI_SUPPLIERS)
                ->default(self::MODE_SINGLE_SUPPLIER),
            'primarySupplier' => Expect::string(),
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $this->loadCommon($builder, $config);
        $this->loadConfigurable($builder, $config);
    }

    private function loadCommon(Nette\DI\ContainerBuilder $builder, $config)
    {
        /** @var ServiceDefinition $priceList */
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

    private function loadConfigurable(Nette\DI\ContainerBuilder $builder, $config)
    {
        /*
        $mode = $config->mode;
        switch ($mode) {
            case self::MODE_SINGLE_SUPPLIER:
                if (!$config->primarySupplier) {
                    $paramName = $this->prefix('primarySupplier');
                    throw new Nette\InvalidStateException("For mode '$mode' a '$paramName' parameter is required");
                }
        }
        */

        /** @var ServiceDefinition $quoteFormFactory */
        $quoteFormFactory = $builder->getDefinitionByType(QuoteFormFactory::class);
        $quoteFormFactory->addSetup('setPrimarySupplier', [$config->primarySupplier]);
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $commissionService = $builder->getDefinition($this->prefix('commissionService'));
        $quoteService = $builder->getDefinition($this->prefix('quoteService'));

        /** @var ServiceDefinition $dashboardStats */
        $dashboardStats = $builder->getDefinition('App.Common.dashboardService');
        $dashboardStats->addSetup(
            '$service->registerStat(?, [?, "countUnresolvedQuotes"])',
            ['quotes', $quoteService]
        );
        $dashboardStats->addSetup(
            '$service->registerStat(?, [?, "countUnresolvedCommissions"])',
            ['commissions', $commissionService]
        );
    }

    public function prefix(string $id): string
    {
        return substr_replace($id, 'App.' . $this->name . '.', substr($id, 0, 1) === '@' ? 1 : 0, 0);
    }
}
