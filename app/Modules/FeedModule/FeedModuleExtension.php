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
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('service'))
            ->setType(FeedService::class);
    }
}
