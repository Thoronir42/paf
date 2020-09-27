<?php declare(strict_types=1);

namespace PAF\Common\Router;

use Nette\Application\Routers\Route;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;

class RoutingExtension extends CompilerExtension
{
    const TAG_ROUTER_MODULE = 'routerModule';
    
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $factoryDefinition = $builder->addDefinition($this->prefix('routerFactory'))
            ->setType(RouterFactory::class)
            ->setArgument('fallbackRouter', new Statement(Route::class, [
                "<presenter>/<action>[/<id>]",
                [
                    'presenter' => 'Common:Homepage',
                    'action' => 'default',
                ],
            ]));

        $routerDefinition = $builder->getDefinition('routing.router');
        if (!$routerDefinition instanceof ServiceDefinition) {
            trigger_error("Could not initialize router");
            return;
        }

        $routerDefinition->setFactory(new Statement('@' . $this->prefix('routerFactory') . '::create'));
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        /** @var ServiceDefinition $routerFactory */
        $routerFactory = $builder->getDefinition($this->prefix('routerFactory'));

        $moduleDefinitions = $builder->findByTag(self::TAG_ROUTER_MODULE);

        $modules = [];
        foreach ($moduleDefinitions as $definitionName => $appModuleName) {
            $modules[$appModuleName] = $builder->getDefinition($definitionName);
        }

        $routerFactory->setArgument('modules', $modules);
    }
}
