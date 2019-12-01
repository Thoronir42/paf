<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Model;

use DateTime;
use LeanMapper\Entity;

/**
 * @property string $id
 * @property CommentThread $thread m:hasOne(thread_id)
 * @property User $user(user_id)
 * @property DateTime $createdOn
 * @property string $text
 */
class Comment extends Entity
{
}
