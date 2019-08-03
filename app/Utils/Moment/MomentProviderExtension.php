<?php declare(strict_types=1);

namespace PAF\Utils\Moment;

use Nette\DI\CompilerExtension;
use Nette\DI\ServiceDefinition;

class MomentProviderExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('provider'))
            ->setType(MomentProvider::class);

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
