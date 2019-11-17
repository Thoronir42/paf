<?php declare(strict_types=1);

namespace SeStep\Typeful;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class TypefulExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'propertyTypes' => Expect::array(),
            'filters' => Expect::structure([
                'displayEntityProperty' => Expect::string(),
                'displayPropertyName' => Expect::string(),
            ]),
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $typeRegister = $builder->addDefinition($this->prefix('typeRegister'))
            ->setType(TypeRegister::class)
            ->setArgument('propertyTypes', $this->getTypesDefinitions($config->propertyTypes));

        $builder->addDefinition($this->prefix('propertyFilter'))
            ->setType(Latte\PropertyFilter::class)
            ->setArgument('typeRegister', $typeRegister);
    }

    private function getTypesDefinitions($configTypes)
    {
        $types = [
            new Statement(Types\IntType::class),
            new Statement(Types\TextType::class),
            new Statement(Types\DateTimeType::class),
        ];

        foreach ($configTypes as $i => $type) {
            if ($type instanceof Statement) {
                $types[] = $type;
                continue;
            }
            if (is_string($type)) {
                if (is_a($type, PropertyType::class, true)) {
                    $types[] = new Statement($type);
                    continue;
                }
            }

            $key = $this->prefix("propertyTypes") . '.' . $i;
            throw new \UnexpectedValueException("Type $key is not recognized");
        }

        return $types;
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

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
                throw new \InvalidArgumentException("Parameter '$paramName' must match `^\w+$` pattern," .
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
