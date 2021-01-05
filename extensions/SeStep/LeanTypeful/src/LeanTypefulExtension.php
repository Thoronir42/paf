<?php declare(strict_types=1);

namespace SeStep\LeanTypeful;

use LeanMapper\Entity;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Caching\Storages\MemoryStorage;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use SeStep\Typeful\DI\RegisterTypeful;

class LeanTypefulExtension extends CompilerExtension
{
    const TAG_LEAN_INFER_ENTITY = 'leanTypeful.typefulLoader';

    use RegisterTypeful;

    public function getConfigSchema(): \Nette\Schema\Schema
    {
        return Expect::structure([
            'schemaName' => Expect::string()->required(),
            'cachePath' => Expect::string(),
        ]);
    }

    public function loadConfiguration()
    {
        $this->registerLeanTypefulPlugin();

        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $builder->addDefinition($this->prefix('reflectionProvider'))
            ->setType(ReflectionProvider::class)
            ->setArgument('schemaName', $config->schemaName);

        $reflectionCache = $builder->addDefinition($this->prefix('reflectionCache'))
            ->setType(Cache::class)
            ->setArgument('storage', $this->getReflectionStorage($config))
            ->setAutowired(false);
        $builder->addDefinition($this->prefix('reflectedDescriptorFactory'))
            ->setType(ReflectedDescriptorFactory::class)
            ->setArgument('cache', $reflectionCache);
    }

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $entitiesToInfer = $builder->findByTag(self::TAG_LEAN_INFER_ENTITY);

        foreach ($entitiesToInfer as $entityDefinitionName => $reflectionDefinition) {
            /** @var ServiceDefinition $definition */
            $definition = $builder->getDefinition($entityDefinitionName);

            $descriptorArguments = $definition->factory->arguments;
            $propertyNamePrefix = $descriptorArguments['propertyNamePrefix'] ?? '';
            $propertiesArguments = array_map(function (Statement $propertyArguments) {
                return $propertyArguments->arguments;
            }, $descriptorArguments['properties']);

            $definition->setFactory('@' . $this->prefix('reflectedDescriptorFactory') . "::create", [
                $reflectionDefinition['entityClass'],
                $reflectionDefinition['reflectionArguments'],
                $propertiesArguments,
                $propertyNamePrefix
            ]);
        }
    }

    private function getReflectionStorage($config): Statement
    {
        $path = $config->cachePath ?? null;
        if (!$path) {
            $storage = new Statement(MemoryStorage::class);
        } else {
            if (file_exists($path) && !is_dir($path)) {
                unlink($path);
            }
            if (!file_exists($path)) {
                mkdir($path);
            }
            $storage = new Statement(FileStorage::class, [$path]);
        }

        return $storage;
    }

    private function registerLeanTypefulPlugin()
    {
        $formatToArray = function ($value) {
            if (is_string($value)) {
                return [
                    'entityClass' => $value,
                ];
            }

            return $value;
        };

        $pluginConfigSchema = Expect::anyOf(
            Expect::string(),
            Expect::structure([
                'entityClass' => Expect::string()->assert(function ($value) {
                    return is_a($value, Entity::class, true);
                }, "Value must be a class descending " . Entity::class),
                'reflectionArguments' => Expect::array(),
            ]),
        )
            ->before($formatToArray)
            ->castTo('array');

        $this->registerTypefulEntityPlugin('leanInferEntity', $pluginConfigSchema, self::TAG_LEAN_INFER_ENTITY);
    }
}
