<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use LeanMapper\Entity;

/**
 * @property int $id
 * @property string $characterName
 * @property string $type m:enum(ProductType::TYPE_*)
 * @property string $characterDescription
 *
 * @see ProductType possible type values
 */
class Specification extends Entity
{

}
