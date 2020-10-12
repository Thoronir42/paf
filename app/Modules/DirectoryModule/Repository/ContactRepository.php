<?php declare(strict_types=1);

namespace PAF\Modules\DirectoryModule\Repository;

use LeanMapper\Entity;
use PAF\Common\Lean\BaseRepository;

class ContactRepository extends BaseRepository
{
    protected function isUnique(Entity $entity)
    {
        $data = $entity->getData(['person', 'type', 'value']);
        unset($data[$this->getPrimaryKey()]);

        return is_null($this->findOneBy($data));
    }
}
