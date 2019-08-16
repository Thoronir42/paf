<?php declare(strict_types=1);

namespace PAF\Common\Router;

use Nette\Routing\Router;

abstract class RouterModule
{
    abstract public function getRoutes(): Router;
}
