<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Model;

use LeanMapper\Entity;
use Nette\InvalidStateException;
use PAF\Modules\DirectoryModule\Model\Person;

/**
 * @property string $id
 * @property string $type m:enum(self::TYPE_*)
 * @property string $username
 * @property string $password
 * @property \DateTime $registered
 * @property \DateTime|null $lastActivity
 *
 * @property Person|null $person m:belongsToOne(user_id)
 */
class User extends Entity
{
    public const TYPE_PERSON = 'person';
    public const TYPE_SYSTEM = 'system';

    public function setType(string $type)
    {
        if (!$this->isDetached()) {
            throw new InvalidStateException("Can not set type of existing user");
        }

        $this->row->type = $type;
    }
}
