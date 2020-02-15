<?php declare(strict_types=1);

namespace PAF\Common\AuditTrail;

use Nette\DI\CompilerExtension;
use PAF\Common\AuditTrail\Facade\AuditTrailService;
use PAF\Common\AuditTrail\Repository\EntryRepository;
use SeStep\EntityIds\Generator\SingleTypeIdGenerator;

class AuditTrailExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $idGenerator = $builder->addDefinition($this->prefix('eventIdGenerator'))
            ->setType(SingleTypeIdGenerator::class)
            ->setArguments([AuditTrailService::class, $builder->getDefinition('entityIds.charSet'), 5])
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('eventRepository'))
            ->setType(EntryRepository::class)
            ->addSetup('bindIdGenerator', [$idGenerator]);

        $builder->addDefinition($this->prefix('service'))
            ->setType(AuditTrailService::class);
    }
}
