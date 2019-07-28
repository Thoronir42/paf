<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use LeanMapper\Entity;

/**
 * @property int $id
 * @property PafCase $case m:hasOne(case_id)
 *
 * @property int $head
 * @property int $body
 * @property int $armSleeves
 * @property int $paws
 * @property int $tail
 * @property int $legSleeves
 * @property int $hindPaws
 */
class FursuitProgress extends Entity
{
    const NOT_INTERESTED = -1;
}
