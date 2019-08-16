<?php declare(strict_types=1);

namespace PAF\Common\Router;

use Nette\Application\Routers\RouteList;
use Nette\InvalidStateException;
use Nette\Routing\Router;

final class RouterFactory
{
    /** @var RouterModule[] */
    private $modules = [];
    /** @var Router */
    private $fallbackRouter;

    public function __construct(Router $fallbackRouter)
    {

        $this->fallbackRouter = $fallbackRouter;
    }

    public function addModule(string $name, RouterModule $module)
    {
        if (isset($this->modules[$name])) {
            throw new InvalidStateException("Router module '$name' already exists");
        }

        $this->modules[$name] = $module;
    }

    /** @return Router */
    public function create()
    {
        $router = new RouteList();

        foreach ($this->modules as $module) {
            $router[] = $module->getRoutes();
        }

        $router[] = $this->fallbackRouter;

        return $router;
    }
}
