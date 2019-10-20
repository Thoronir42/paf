<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use PAF\Modules\FeedModule\Components\FeedControl\FeedControlFactory;
use PAF\Modules\FeedModule\Components\FeedControl\FeedEntryControlFactory;
use PAF\Modules\FeedModule\Service\FeedService;

class FeedModuleExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'entryControlClasses' => Expect::arrayOf(Expect::string()),
        ]);
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig();

        $builder->addDefinition($this->prefix('service'))
            ->setType(FeedService::class);

        $entryControlFactory = $builder->addDefinition($this->prefix('entryControlFactory'))
            ->setType(FeedEntryControlFactory::class)
            ->setArgument('typeToClass', $config->entryControlClasses)
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('feedFactory'))
            ->setType(FeedControlFactory::class)
            ->setArguments([$entryControlFactory]);
    }
}
