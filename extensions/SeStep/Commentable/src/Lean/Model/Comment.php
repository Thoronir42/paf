<?php declare(strict_types=1);

namespace SeStep\Commentable\Lean\Model;

use DateTime;
use LeanMapper\Entity;
use PAF\Modules\CommonModule\Model\User;

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
