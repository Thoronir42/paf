<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;

use Nette\Utils\DateTime;
use PAF\Common\Model\BaseEntity;
use PAF\Modules\CommonModule\Model\Person;
use SeStep\FileAttachable\Model\UserFileThread;

/**
 * @property int $id
 * @property Person $issuer m:hasOne(issuer_person_id)
 * @property string $slug
 * @property string $status m:enum(self::STATUS*)
 * @property DateTime $dateCreated
 * @property Specification $specification m:hasOne(specification_id)
 * @property UserFileThread|null $references m:hasOne(references_thread_id)
 */
class Quote extends BaseEntity
{
    const STATUS_NEW = 'new';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';


    public static function getStatuses()
    {
        return [
            self::STATUS_NEW,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
        ];
    }
}
