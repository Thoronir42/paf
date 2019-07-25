<?php declare(strict_types=1);

namespace SeStep\Commentable\Lean\Model;


use DateTime;
use PAF\Common\Model\BaseEntity;

/**
 * @property int $id
 * @property CommentThread $thread m:hasOne(thread_id)
 * @property DateTime $createdOn
 * @property string $text
 */
class Comment extends BaseEntity
{
}
