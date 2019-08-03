<?php declare(strict_types=1);

namespace SeStep\Commentable\Lean\Model;

use PAF\Common\Model\BaseEntity;

/**
 * @property int $id
 * @property Comment[] $comments m:belongsToMany(thread_id)
 *
 */
class CommentThread extends BaseEntity
{
}
