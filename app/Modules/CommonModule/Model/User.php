<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Model;

use Nette\InvalidStateException;
use PAF\Common\Model\BaseEntity;
use PAF\Modules\PortfolioModule\Model\Fursuit;

/**
 * @property int $id
 * @property string $type m:enum(self::TYPE_*)
 * @property string $username
 * @property string $password
 * @property \DateTime $registered
 * @property \DateTime|null $lastActivity
 *
 * @property Fursuit[] $fursuits
 */
class User extends BaseEntity
{
    public const TYPE_PERSON = 'person';

    public function setType(string $type)
    {
        if (!$this->isDetached()) {
            throw new InvalidStateException("Can not set type of existing user");
        }

        $this->row->type = $type;
    }
}
