<?php declare(strict_types=1);

namespace SeStep\LeanCommon\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use SeStep\LeanCommon\BaseRepository;

class RepositoryExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        foreach ($builder->findByType(BaseRepository::class) as $definition) {
            if (!$definition instanceof ServiceDefinition) {
                continue;
            }
            $repoClass = $definition->getType();
            if (method_exists($repoClass, 'injectTypefulRegistry')) {
                $definition->addSetup('injectTypefulRegistry');
            }
            if (method_exists($repoClass, 'registerUniquenessEvents')) {
                $definition->addSetup('registerUniquenessEvents');
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
