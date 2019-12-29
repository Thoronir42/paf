<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use LeanMapper\Entity;
use PAF\Modules\CommonModule\Model\UserFileThread;

/**
 * @property int $id
 * @property string $characterName
 * @property string $type m:enum(ProductType::TYPE_*)
 * @property string $characterDescription
 *
 * @property UserFileThread|null $references m:hasOne(references_thread_id)
 *
 * @see ProductType possible type values
 */
class Specification extends Entity
{

}
