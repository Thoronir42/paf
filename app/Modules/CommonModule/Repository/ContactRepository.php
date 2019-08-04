<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Repository;

use LeanMapper\Entity;
use PAF\Common\Model\BaseRepository;

class ContactRepository extends BaseRepository
{
    protected function isUnique(Entity $entity)
    {
        $data = $entity->getRowData();
        unset($data[$this->getPrimaryKey()]);

        return is_null($this->findOneBy($data));
    }

}
