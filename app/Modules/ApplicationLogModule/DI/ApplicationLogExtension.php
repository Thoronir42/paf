<?php declare(strict_types=1);

namespace PAF\Modules\ApplicationLogModule\DI;

use Nette\DI\CompilerExtension;
use PAF\Modules\ApplicationLogModule\Facade\AppLog;
use PAF\Modules\ApplicationLogModule\Repository\EventRepository;
use SeStep\EntityIds\TextIdGenerator;

class ApplicationLogExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $idGenerator = $builder->addDefinition($this->prefix('eventIdGenerator'))
            ->setType(TextIdGenerator::class)
            ->setArguments([$builder->getDefinition('entityIds.charSet'), 5])
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('eventRepository'))
            ->setType(EventRepository::class)
            ->addSetup('setIdGenerator', [$idGenerator]);

        $builder->addDefinition($this->prefix('log'))
            ->setType(AppLog::class);
    }
}
