<?php declare(strict_types=1);

namespace SeStep\Typeful;

use InvalidArgumentException;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class TypefulExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'filters' => Expect::structure([
                'displayEntityProperty' => Expect::string(),
                'displayPropertyName' => Expect::string(),
            ]),
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $typeRegister = $builder->addDefinition($this->prefix('typeRegister'))
            ->setType(TypeRegister::class);

        $builder->addDefinition($this->prefix('entityDescriptorRegister'))
            ->setType(EntityDescriptorRegistry::class);

        $builder->addDefinition($this->prefix('propertyFilter'))
            ->setType(Latte\PropertyFilter::class)
            ->setArgument('typeRegister', $typeRegister);
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $typeServiceNames = $builder->findByTag('typeful.propertyType');
        $types = [
            new Statement(Types\IntType::class),
            new Statement(Types\TextType::class),
            new Statement(Types\DateType::class),
            new Statement(Types\DateTimeType::class),
        ];
        foreach (array_keys($typeServiceNames) as $typeServiceName) {
            $types[] = $builder->getDefinition($typeServiceName);
        }

        /** @var ServiceDefinition $typeRegister */
        $typeRegister = $builder->getDefinition($this->prefix('typeRegister'));
        $typeRegister->setArgument('propertyTypes', $types);


        $descriptors = [];
        foreach ($builder->findByTag('typeful.entity') as $service => $entityClass) {
            $descriptors[$entityClass] = $builder->getDefinition($service);
        }

        /** @var ServiceDefinition $entityDescriptorRegister */
        $entityDescriptorRegister = $builder->getDefinition($this->prefix('entityDescriptorRegister'));
        $entityDescriptorRegister->setArgument('descriptors', $descriptors);

        if (!empty($config->filters)) {
            $this->registerFilters($builder->getDefinition($this->prefix('propertyFilter')), $config->filters);
        }
    }

    private function registerFilters($filterService, $filterNamesConfig)
    {
        /** @var FactoryDefinition $latteFactory */
        $latteFactory = $this->getContainerBuilder()->getDefinition('nette.latteFactory');
        $latteFactory = $latteFactory->getResultDefinition();

        foreach ($filterNamesConfig as $filterMethod => $registerName) {
            if (!preg_match('/^\w+$/', $registerName)) {
                $paramName = $this->prefix("filters.$filterMethod");
                throw new InvalidArgumentException("Parameter '$paramName' must match `^\w+$` pattern," .
                    " got '$registerName'");
            }

            $latteFactory->addSetup(
                "\$service->addFilter(?, [?, ?])",
                [
                    $registerName,
                    $filterService,
                    $filterMethod,
                ]
            );
        }
    }
}
