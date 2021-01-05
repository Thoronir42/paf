<?php declare(strict_types=1);

namespace PAF\Common\Lean\DI;

use Nette;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use PAF\Common\Lean\LeanQueryFilter;
use PAF\Common\Lean\LeanSnapshots;

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
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $builder->addDefinition($this->prefix('mapper'))
            ->setType('LeanMapper\DefaultMapper');

        $builder->addDefinition($this->prefix('entityFactory'))
            ->setType('LeanMapper\DefaultEntityFactory');

        $this->loadDefinitionsFromConfig([
            'snapshots' => LeanSnapshots::class,
            'queryFilter' => LeanQueryFilter::class,
        ]);

        $connection = $builder->addDefinition($this->prefix('connection'))
            ->setFactory('LeanMapper\Connection', [$config['db']]);

        if (isset($config['db']['flags'])) {
            $flags = 0;
            foreach ((array)$config['db']['flags'] as $flag) {
                $flags |= constant($flag);
            }
            $config['db']['flags'] = $flags;
        }

        if (class_exists('Tracy\Debugger') && $builder->parameters['debugMode'] && $config['profiler']) {
            $panel = $builder->addDefinition($this->prefix('panel'))
                ->setType('Dibi\Bridges\Tracy\Panel');
            $connection->addSetup([$panel, 'register'], [$connection]);
            if ($config['logFile']) {
                $fileLogger = $builder->addDefinition($this->prefix('fileLogger'))
                    ->setType('Dibi\Loggers\FileLogger', [$config['logFile']]);
                $connection->addSetup('$service->onEvent[] = ?', [
                    [$fileLogger, 'logEvent'],
                ]);
            }
        }
    }
}
