<?php declare(strict_types=1);

namespace PAF\Common\AuditTrail\Entity;

use DateTime;
use LeanMapper\Entity;
use PAF\Modules\CommonModule\Model\User;

/**
 * AuditTrail entity
 *
 * @property string $id
 * @property string $subject id of subject where the event happened
 * @property DateTime $instant
 * @property User|null $actor m:hasOne(actor)
 * @property string $type
 * @property array $parameters
 */
class Entry extends Entity
{
    private $paramsCache;

    public function getParameters(): array
    {
        return $this->paramsCache ?: $this->paramsCache = json_decode($this->row->parameters, true);
    }

    public function setParameters(array $parameters)
    {
        $this->row->parameters = json_encode($parameters);
        $this->paramsCache = $parameters;
    }

    public function setActorId(string $id = null)
    {
        $this->row->actor = $id;
    }
}
