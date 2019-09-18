<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use LeanMapper\Entity;
use PAF\Common\Model\BaseEntity;
use PAF\Modules\PortfolioModule\Model\Fursuit;

/**
 * @property int $id
 * @property string $characterName
 * @property string $type m:enum(Fursuit::TYPE_*)
 * @property string $characterDescription
 *
 * @see Fursuit possible type values
 */
class Specification extends BaseEntity
{

}
