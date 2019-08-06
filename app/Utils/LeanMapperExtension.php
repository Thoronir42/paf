<?php declare(strict_types=1);
namespace PAF\Utils;

use Nette;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;

class LeanMapperExtension extends \LeanMapper\Bridges\Nette\DI\LeanMapperExtension
{
    public function getConfigSchema(): Nette\Schema\Schema
    {
        return Expect::structure([
            'db' => Expect::array()->default([]),
            'profiler' => Expect::bool(true),
            'logFile' => Expect::string(),
        ])->castTo('array');
    }


    public function getConfig()
    {
        return CompilerExtension::getConfig();
    }

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();

        $container->addDefinition($this->prefix('mapper'))
            ->setClass('LeanMapper\DefaultMapper');

        $container->addDefinition($this->prefix('entityFactory'))
            ->setClass('LeanMapper\DefaultEntityFactory');

        $connection = $container->addDefinition($this->prefix('connection'))
            ->setFactory('LeanMapper\Connection', [$config['db']]);

        if (isset($config['db']['flags'])) {
            $flags = 0;
            foreach ((array)$config['db']['flags'] as $flag) {
                $flags |= constant($flag);
            }
            $config['db']['flags'] = $flags;
        }

        if (class_exists('Tracy\Debugger') && $container->parameters['debugMode'] && $config['profiler']) {
            $panel = $container->addDefinition($this->prefix('panel'))->setClass('Dibi\Bridges\Tracy\Panel');
            $connection->addSetup([$panel, 'register'], [$connection]);
            if ($config['logFile']) {
                $fileLogger = $container->addDefinition($this->prefix('fileLogger'))
                    ->setClass('Dibi\Loggers\FileLogger', [$config['logFile']]);
                $connection->addSetup('$service->onEvent[] = ?', [
                    [$fileLogger, 'logEvent'],
                ]);
            }
        }
    }
}