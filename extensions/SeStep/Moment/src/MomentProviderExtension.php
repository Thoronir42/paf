<?php declare(strict_types=1);

namespace SeStep\Moment;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\DI\ServiceDefinition;

class MomentProviderExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('provider'))
            ->setType(RelativeMomentProvider::class)
            ->setArgument('now', new Statement(\DateTime::class));
    }
    
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $builder->getDefinition($this->prefix('provider'));

        foreach ($builder->getDefinitions() as $definition) {
            if (!$definition instanceof ServiceDefinition) {
                continue;
            }
            $class = $definition->getType();
            if (!class_exists($class)) {
                continue;
            }
            if (!in_array(HasMomentProvider::class, class_uses($class))) {
                continue;
            }

            $definition->addSetup('setMomentProvider');
        }
    }
}
