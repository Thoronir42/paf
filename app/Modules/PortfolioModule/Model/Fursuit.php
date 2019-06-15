<?php declare(strict_types=1);

namespace PAF\Modules\PortfolioModule\Model;


use PAF\Common\Model\BaseEntity;
use PAF\Common\Model\Traits\Slug;
use Nette\Utils\DateTime;
use PAF\Modules\CommonModule\Model\User;

/**
 * @property string $name
 * @property string $type m:enum(self::TYPE_*)
 * @property User $owner
 * @property DateTime $issuedOn
 * @property DateTime $completedOn
 */
class Fursuit extends BaseEntity
{
    const TYPE_PARTIAL = 'partial';
    const TYPE_HALF_SUIT = 'halfsuit';
    const TYPE_FULL_SUIT = 'fullsuit';

    use Slug;

    public static function getTypes()
    {
        return [
            self::TYPE_PARTIAL => self::TYPE_PARTIAL,
            self::TYPE_HALF_SUIT => self::TYPE_HALF_SUIT,
            self::TYPE_FULL_SUIT => self::TYPE_FULL_SUIT,
        ];
    }
}
