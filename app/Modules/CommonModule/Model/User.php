<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Model;

use Nette\Utils\DateTime;
use PAF\Common\Model\BaseEntity;
use PAF\Modules\PortfolioModule\Model\Fursuit;

/**
 * @property int $id
 * @property string $username
 * @property string $password
 * @property DateTime $registered
 * @property DateTime|null $lastActivity
 * @property Contact[] $contact m:belongsToMany(user_id)
 *
 * @property Fursuit[] $fursuits
 */
class User extends BaseEntity
{
}
