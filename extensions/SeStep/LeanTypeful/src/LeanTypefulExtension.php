<?php declare(strict_types=1);

namespace SeStep\LeanTypeful;

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
    const TAG_LEAN_TYPEFUL_LOADER = 'leanTypeful.typefulLoader';

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
        $this->registerTypefulEntityPlugin('leanInferTable', Expect::string(), self::TAG_LEAN_TYPEFUL_LOADER);

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
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();

        $entitiesToInfer = $builder->findByTag(self::TAG_LEAN_TYPEFUL_LOADER);

        foreach ($entitiesToInfer as $entityDefinitionName => $reflectionDefinition) {
            if (is_string($reflectionDefinition)) {
                $table = $reflectionDefinition;
                $reflectionArguments = [];
            } else {
                $table = $reflectionDefinition->table;
                $reflectionArguments = $reflectionDefinition->arguments ?? [];
            }

            /** @var ServiceDefinition $definition */
            $definition = $builder->getDefinition($entityDefinitionName);

            $descriptorArguments = $definition->factory->arguments;
            $propertyNamePrefix = $descriptorArguments['propertyNamePrefix'] ?? '';
            $propertiesArguments = array_map(function (Statement $propertyArguments) {
                return $propertyArguments->arguments;
            }, $descriptorArguments['properties']);

            $definition->setFactory('@' . $this->prefix('reflectedDescriptorFactory') . "::create", [
                $table,
                $reflectionArguments,
                $propertiesArguments,
                $propertyNamePrefix
            ]);
        }
    }

    private function getReflectionStorage($config)
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
}
