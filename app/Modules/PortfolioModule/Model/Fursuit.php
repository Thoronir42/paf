<?php declare(strict_types=1);

namespace PAF\Modules\PortfolioModule\Model;

use LeanMapper\Entity;
use Nette\Utils\DateTime;
use PAF\Modules\CommonModule\Model\User;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $type m:enum(self::TYPE_*)
 * @property User $owner
 * @property DateTime $issuedOn
 * @property DateTime $completedOn
 */
class Fursuit extends Entity
{
    const TYPE_PARTIAL = 'partial';
    const TYPE_HALF_SUIT = 'halfsuit';
    const TYPE_FULL_SUIT = 'fullsuit';

    public static function getTypes()
    {
        return [
            self::TYPE_PARTIAL => self::TYPE_PARTIAL,
            self::TYPE_HALF_SUIT => self::TYPE_HALF_SUIT,
            self::TYPE_FULL_SUIT => self::TYPE_FULL_SUIT,
        ];
    }
}
