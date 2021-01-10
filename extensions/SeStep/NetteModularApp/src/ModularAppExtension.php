<?php declare(strict_types=1);

namespace SeStep\NetteModularApp;

use Nette;
use Nette\Application\Routers\Route;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\DI\Helpers;
use SeStep\ModularLeanMapper\ModularMapper;
use SeStep\Typeful\DI\RegisterTypeful;

class ModularAppExtension extends CompilerExtension
{
    use RegisterTypeful;

    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::structure([
            'routerMode' => Nette\Schema\Expect::anyOf('app', 'api')->required(),
            'defaultAppRoute' => Nette\Schema\Expect::string()->nullable(),
            'modules' => Nette\Schema\Expect::arrayOf(self::getModuleConfigurationStructure()),
            'mapperConventions' => Nette\Schema\Expect::mixed()->required(),
            'mapperAdditionalModules' => Nette\Schema\Expect::arrayOf(Nette\Schema\Expect::mixed()),
        ]);
    }

    public function loadConfiguration()
    {
        $config = $this->getConfig();
        $builder = $this->getContainerBuilder();

        $this->initExtensions($config, $builder);
        $this->initStaticConfig($config, $builder);
        $this->initRouting($config, $builder);
        $this->initLeanMapper($config, $builder);
    }

    private function initExtensions(object $config, Nette\DI\ContainerBuilder $builder): void
    {
        foreach ($config->modules as $name => $appModule) {
            if (!isset($appModule->extension)) {
                continue;
            }

            $extension = $appModule->extension;
            if (!is_string($extension) || !class_exists($extension)) {
                throw new Nette\NotImplementedException("Registering extensions can be done only via classnames");
            }
            $this->compiler->addExtension("App.$name", new $extension());
        }
    }

    private function initStaticConfig(object $config, Nette\DI\ContainerBuilder $builder): void
    {
        foreach ($config->modules as $name => $appModule) {
            if (!isset($appModule->staticConfig)) {
                continue;
            }
            $config = $this->loadFromFile($appModule->staticConfig);
            if (isset($config['services'])) {
                $this->defineStaticServices("App.$name", $config['services'], $builder);
            }
            if (isset($config['typeful'])) {
                $this->getTypefulExtension()->addTypefulModule("App.$name", $config['typeful']);
            }

            $unrecognizedKeys = array_diff(array_keys($config), ['services', 'typeful']);
            if (!empty($unrecognizedKeys)) {
                throw new Nette\UnexpectedValueException("Static config of module '$name' contains unrecognized"
                . " keys: " . implode(", ", $unrecognizedKeys));
            }
        }
    }

    private function defineStaticServices(string $moduleName, array $services, Nette\DI\ContainerBuilder $builder): void
    {
        $res = [];
        foreach ($services as $key => $config) {
            $key = is_string($key) ? $moduleName . '.' . $key : $key;
            $res[$key] = Helpers::prefixServiceName($config, $moduleName);
        }
        $this->compiler->loadDefinitionsFromConfig($res);
    }

    private function initRouting(object $config, Nette\DI\ContainerBuilder $builder): void
    {
        $routerFactory = $builder->addDefinition($this->prefix('routerFactory'))
            ->setType(Routing\RouterFactory::class)
            ->setArgument('modules', $this->formatRouterModuleDefinitions($config, $builder));
        if ($config->defaultAppRoute) {
            $routerFactory->addSetup('setFallbackRouter', [
                new Statement(Route::class, [
                    "<presenter>/<action>[/<id>]",
                    ['presenter' => $config->defaultAppRoute, 'action' => 'default'],
                ]),
            ]);
        }

        $builder->removeDefinition('routing.router');
        $builder->addDefinition('routing.router')
            ->setFactory(new Statement('@' . $this->prefix('routerFactory') . '::create'));
    }

    private function formatRouterModuleDefinitions(object $config, Nette\DI\ContainerBuilder $builder): array
    {
        $routerKey = $config->routerMode . 'Router';

        $modules = [];
        foreach ($config->modules as $name => $appModule) {
            if (!isset($appModule->$routerKey)) {
                continue;
            }
            $modules[$name] = $builder->addDefinition($this->prefix("routerModule.$name"))
                ->setFactory($appModule->$routerKey);
        }

        return $modules;
    }

    private function initLeanMapper(object $config, Nette\DI\ContainerBuilder $builder): void
    {
        $builder->removeDefinition('leanMapper.mapper');

        $mapperModules = $this->formatMapperModules($config->modules);
        foreach ($config->mapperAdditionalModules ?? [] as $name => $module) {
            $mapperModules[$name] = $module;
        }

        $builder->addDefinition('leanMapper.mapper')
            ->setType(ModularMapper::class)
            ->setArguments([
                'mapper' => $config->mapperConventions,
                'modules' => $mapperModules,
            ]);
    }

    /**
     * @param object[] $modules
     * @return Nette\DI\Definitions\Statement[]
     */
    private function formatMapperModules(array $modules): array
    {
        $mapperModules = [];
        foreach ($modules as $name => $appModule) {
            $mapperModule = $appModule->leanMapperModule ?? null;
            if (!$mapperModule) {
                continue;
            }

            $key = $appModule->leanMapperPrefix ?? $name . '__';

            if (!($mapperModule instanceof Nette\DI\Definitions\Statement)) {
                throw new Nette\UnexpectedValueException();
            }
            $mapperModules[$key] = $mapperModule;
        }

        return $mapperModules;
    }

    private static function getModuleConfigurationStructure(): Nette\Schema\Schema
    {
        return Nette\Schema\Expect::structure([
            'extension' => Nette\Schema\Expect::mixed(),
            'staticConfig' => Nette\Schema\Expect::string(),
            'appRouter' => Nette\Schema\Expect::mixed(),
            'apiRouter' => Nette\Schema\Expect::mixed(),
            'leanMapperModule' => Nette\Schema\Expect::mixed(),
            'leanMapperPrefix' => Nette\Schema\Expect::string(),
        ]);
    }
}
