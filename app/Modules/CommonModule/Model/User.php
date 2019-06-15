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
 * @property DateTime $lastActivity
 * @property string $status
 *
 * @property Fursuit[] $fursuits
 */
class User extends BaseEntity
{
}
