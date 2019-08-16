<?php declare(strict_types=1);

namespace PAF\Common\Router;

use Nette\Application\Routers\Route;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class RoutingExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'modules' => Expect::arrayOf(Statement::class),
        ])->castTo('array');
    }


    public function loadConfiguration()
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();

        $factoryDefinition = $builder->addDefinition($this->prefix('routerFactory'))
            ->setType(RouterFactory::class)
            ->setArgument('fallbackRouter', new Statement(Route::class, [
                "<presenter>/<action>[/<id>]",
                [
                    'presenter' => 'Common:Homepage',
                    'action' => 'default',
                ]
            ]));
        
        foreach ($config['modules'] as $key => $module) {
            $factoryDefinition->addSetup('addModule', [$key, $module]);
        }

        $routerDefinition = $builder->getDefinition('routing.router');
        if (!$routerDefinition instanceof ServiceDefinition) {
            trigger_error("Could not initialize router");
            return;
        }

        $routerDefinition->setFactory(new Statement('@' . $this->prefix('routerFactory') . '::create'));
    }
}
