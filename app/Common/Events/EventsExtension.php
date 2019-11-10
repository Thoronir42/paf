<?php declare(strict_types=1);

namespace PAF\Common\Events;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventsExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'subscribers' => Expect::array()
        ]);
    }
    
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $dispatcher = $builder->addDefinition($this->prefix('dispatcher'))
            ->setType(EventDispatcher::class);

        foreach ($config->subscribers as $subscriber) {
            $dispatcher->addSetup('$service->addSubscriber(?)', [$subscriber]);
        }
    }
}
