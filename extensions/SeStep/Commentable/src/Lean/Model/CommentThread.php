<?php declare(strict_types=1);

namespace SeStep\Commentable\Lean\Model;

use LeanMapper\Entity;

/**
 * @property string $id
 * @property Comment[] $comments m:belongsToMany(thread_id)
 *
 */
class CommentThread extends Entity
{
}
