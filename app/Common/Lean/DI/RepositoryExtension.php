<?php declare(strict_types=1);


namespace PAF\Common\Lean\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use PAF\Common\Lean\BaseRepository;

class RepositoryExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        foreach ($builder->findByType(BaseRepository::class) as $definition) {
            if (!$definition instanceof ServiceDefinition) {
                continue;
            }

            $definition->addSetup('$mapper = ?;', [$builder->getDefinition('leanMapper.mapper')]);
            $definition->addSetup('$entityClass = $mapper->getEntityClass($mapper->getTableByRepositoryClass(?))', [
                $definition->getType()
            ]);
            $definition->addSetup(<<<PHP
\$idGenerator = ?;
if(\$idGenerator->hasType(\$entityClass)) {
    \$service->bindIdGenerator(\$idGenerator);
}
PHP, [$builder->getDefinition('entityIds.idGenerator')]);
        }
    }
}
