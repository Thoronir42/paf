<?php declare(strict_types=1);

namespace PAF\Common\Feed;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use PAF\Common\Feed\Components\FeedControl\EntryControlFactory;
use PAF\Common\Feed\Components\FeedControl\FeedControlFactory;
use PAF\Common\Feed\Components\FeedControl\FeedEntryControl;
use PAF\Common\Feed\Components\FeedControl\FeedEntryControlFactory;
use PAF\Common\Feed\Service\FeedService;

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
            ->setType(FeedService::class);

        $entryControlFactory = $builder->addDefinition($this->prefix('entryControlFactory'))
            ->setType(EntryControlFactory::class)
            ->setArgument('controlTypes', $config->entryControlClasses)
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('feedFactory'))
            ->setType(FeedControlFactory::class)
            ->setArguments([$entryControlFactory]);
    }

    private function validateControlClasses(array &$controlClasses)
    {
        $builder = $this->getContainerBuilder();
        foreach ($controlClasses as $type => $controlClass) {
            if (is_string($controlClass)) {
                if (is_a($controlClass, FeedEntryControl::class, true)) {
                    continue;
                }

                if ($controlClass[0] == '@') {
                    $definition = $builder->getDefinition(substr($controlClass, 1));
                    if ($definition instanceof ServiceDefinition
                        && is_a($definition->getType(), FeedEntryControlFactory::class, true)) {
                        continue;
                    }
                }
            }

            if ($controlClass instanceof Statement) {
                if (is_a($controlClass->getEntity(), FeedEntryControlFactory::class, true)) {
                    continue;
                }
            }

            throw new \UnexpectedValueException("Invalid control class for '$type'");
        }
    }
}
