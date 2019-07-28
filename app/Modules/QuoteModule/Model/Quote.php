<?php declare(strict_types=1);

namespace PAF\Modules\QuoteModule\Model;

use Nette\Utils\DateTime;
use PAF\Common\Model\BaseEntity;
use PAF\Modules\CommonModule\Model\Contact;
use PAF\Modules\CommissionModule\Model\FursuitSpecification;
use PAF\Modules\CommonModule\Model\User;
use SeStep\FileAttachable\Model\UserFileThread;

/**
 * @property int $id
 * @property User $issuer
 * @property string $slug
 * @property string status m:column(self::STATUS_*)
 * @property DateTime $dateCreated
 * @property Contact $contact
 * @property FursuitSpecification $specification
 * @property UserFileThread $references
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
