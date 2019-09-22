<?php declare(strict_types=1);

namespace SeStep\FileAttachable\Model;

use PAF\Common\Model\BaseEntity;

/**
 * @property int $id
 * @property UserFile[] $files m:belongsToMany(thread_id)
 * @property \DateTime $dateCreated
 */
class UserFileThread extends BaseEntity implements \IteratorAggregate, \Countable
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
