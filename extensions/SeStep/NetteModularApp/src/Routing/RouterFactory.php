<?php declare(strict_types=1);

namespace SeStep\NetteModularApp\Routing;

use Nette\Application\Routers\RouteList;
use Nette\Routing\Router;

final class RouterFactory
{
    private ?Router $fallbackRouter = null;
    /** @var RouterModule[] */
    private array $modules;

    /**
     * @param RouterModule[] $modules associative array of application router modules
     */
    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    public function setFallbackRouter(?Router $fallbackRouter): void
    {
        $this->fallbackRouter = $fallbackRouter;
    }

    /** @return Router */
    public function create()
    {
        $router = new RouteList();

        foreach ($this->modules as $appModuleName => $routerModule) {
            $moduleRouteList = new RouteList($appModuleName);
            $routerModule->setRoutes($moduleRouteList);
            $router->add($moduleRouteList);
        }

        if ($this->fallbackRouter) {
            $router[] = $this->fallbackRouter;
        }


        return $router;
    }
}
