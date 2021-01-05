<?php declare(strict_types=1);

namespace PAF\Modules\Feed;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use UnexpectedValueException;

class FeedExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'entryControlClasses' => Expect::array(),
        ]);
    }

    public function beforeCompile()
    {
        $config = parent::getConfig();
        $this->validateControlClasses($config->entryControlClasses);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $builder->addDefinition($this->prefix('service'))
            ->setType(Service\FeedService::class);

        $entryControlFactory = $builder->addDefinition($this->prefix('entryControlFactory'))
            ->setType(Components\FeedControl\EntryControlFactory::class)
            ->setArgument('controlTypes', $config->entryControlClasses)
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('feedFactory'))
            ->setType(Components\FeedControl\FeedControlFactory::class)
            ->setArguments([$entryControlFactory]);
    }

    private function validateControlClasses(array $controlClasses)
    {
        $builder = $this->getContainerBuilder();
        foreach ($controlClasses as $type => $controlClass) {
            if (is_string($controlClass)) {
                if (is_a($controlClass, Components\FeedControl\FeedEntryControl::class, true)) {
                    continue;
                }

                if ($controlClass[0] == '@') {
                    $definition = $builder->getDefinition(substr($controlClass, 1));
                    if ($definition instanceof ServiceDefinition
                        && is_a($definition->getType(), Components\FeedControl\FeedEntryControlFactory::class, true)) {
                        continue;
                    }
                }
            }

            if ($controlClass instanceof Statement) {
                if (is_a($controlClass->getEntity(), Components\FeedControl\FeedEntryControlFactory::class, true)) {
                    continue;
                }
            }

            throw new UnexpectedValueException("Invalid control class for '$type'");
        }
    }
}
