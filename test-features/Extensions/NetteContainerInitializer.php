<?php declare(strict_types=1);

namespace Behat\PAF\Extensions;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Nette\DI\Container;

class NetteContainerInitializer implements ContextInitializer
{
    /** @var Container */
    private $container;

    public function __construct()
    {
        $this->container = require_once __DIR__ . '/../../app/bootstrap.php';
    }


    /**
     * Initializes provided context.
     *
     * @param Context $context
     */
    public function initializeContext(Context $context)
    {
        if ($context instanceof InjectDependencies) {
            $this->container->callInjects($context);
        }
    }
}
