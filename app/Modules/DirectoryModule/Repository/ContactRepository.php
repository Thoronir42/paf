<?php declare(strict_types=1);

namespace PAF\Modules\DirectoryModule\Repository;

use LeanMapper\Entity;
use SeStep\LeanCommon\BaseRepository;

class ContactRepository extends BaseRepository
{
    protected function isUnique(Entity $entity): bool
    {
        $data = $entity->getData(['person', 'type', 'value']);
        unset($data[$this->mapper->getPrimaryKey($this->getTable())]);

        return is_null($this->findOneBy($data));
    }
}
