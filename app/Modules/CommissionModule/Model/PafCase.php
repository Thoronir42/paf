<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Model;


use LeanMapper\Entity;
use Nette\Utils\DateTime;
use PAF\Common\Model\Contact;
use SeStep\Commentable\Lean\Model\CommentThread;

/**
 * @property string $status m:enum(self::STATUS_)
 * @property Contact $contact
 * @property FursuitSpecification $specification
 * @property FursuitProgress $progress
 * @property DateTime $dateAccepted
 * @property DateTime $targetDate
 * @property CommentThread $comments
 *
 */
class PafCase extends Entity
{
    const STATUS_ACCEPTED = "accepted";
    const STATUS_WIP = "wip";
    const STATUS_FINISHED = "finished";
    const STATUS_CANCELLED = "cancelled";

    public static function getStatuses()
    {
        return [
            self::STATUS_ACCEPTED,
            self::STATUS_WIP,
            self::STATUS_FINISHED,
            self::STATUS_CANCELLED,
        ];
    }
}
