<?php declare(strict_types=1);

namespace SeStep\FileAttachable\Model;

use LeanMapper\Entity;
use Nette\InvalidArgumentException;

/**
 * @property UserFileThread|null $thread m:hasOne(thread_id)
 * @property string $filename
 * @property int $size
 */
class UserFile extends Entity
{
    public function validate()
    {
        if (strlen($this->filename) > 420) {
            throw new InvalidArgumentException("Filename must be at most 420 characters long");
        }
        if ($this->size < 0) {
            throw new InvalidArgumentException("File size must be a positive number or zero");
        }
    }
}
