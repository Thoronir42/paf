<?php declare(strict_types=1);

namespace PAF\Modules\OfferModule;


use Nette\DI\CompilerExtension;
use PAF\Common\Router\RoutingExtension;
use SeStep\Typeful\DI\RegisterTypeful;

class OfferModuleExtension extends CompilerExtension
{
    use RegisterTypeful;

    public function loadConfiguration()
    {
        $staticConfig = $this->loadFromFile(__DIR__ . '/offerModule.static.neon');

        $builder = $this->getContainerBuilder();

        $this->loadDefinitionsFromConfig($staticConfig['services']);

        $builder->addDefinition($this->prefix('routerModule'))
            ->setType(Routing\OfferRouterModule::class)
            ->addTag(RoutingExtension::TAG_ROUTER_MODULE, 'Offer');

        $this->registerTypeful($staticConfig['typeful']);
    }
}
