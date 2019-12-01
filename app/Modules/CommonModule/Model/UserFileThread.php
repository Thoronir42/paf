<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Model;

use LeanMapper\Entity;

/**
 * @property int $id
 * @property UserFile[] $files m:belongsToMany(thread_id)
 * @property \DateTime $dateCreated
 */
class UserFileThread extends Entity implements \IteratorAggregate, \Countable
{

    public function getIterator()
    {
        return new \ArrayIterator($this->files);
    }

    public function count()
    {
        return count($this->files);
    }
}
