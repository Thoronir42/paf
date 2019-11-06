<?php declare(strict_types=1);

namespace PAF\Modules\AuditTrailModule\DI;

use Nette\DI\CompilerExtension;
use PAF\Modules\AuditTrailModule\Facade\AuditTrailService;
use PAF\Modules\AuditTrailModule\Repository\EntryRepository;
use SeStep\EntityIds\TextIdGenerator;

class AuditTrailExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $idGenerator = $builder->addDefinition($this->prefix('eventIdGenerator'))
            ->setType(TextIdGenerator::class)
            ->setArguments([$builder->getDefinition('entityIds.charSet'), 5])
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('eventRepository'))
            ->setType(EntryRepository::class)
            ->addSetup('setIdGenerator', [$idGenerator]);

        $builder->addDefinition($this->prefix('service'))
            ->setType(AuditTrailService::class);
    }
}
