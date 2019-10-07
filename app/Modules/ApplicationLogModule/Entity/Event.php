<?php declare(strict_types=1);

namespace PAF\Modules\ApplicationLogModule\Entity;

use DateTime;
use PAF\Common\Model\BaseEntity;

/**
 * Application event entity
 *
 * @property string $id
 * @property string $subject id of subject where the event happened
 * @property DateTime $instant
 * @property string|null $actor
 * @property string $type
 * @property string $parameters
 */
class Event extends BaseEntity
{

}
