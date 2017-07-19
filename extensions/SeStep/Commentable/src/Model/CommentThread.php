<?php
/**
 * Created by PhpStorm.
 * User: Skoro
 * Date: 03.07.2017
 * Time: 18:12
 */

namespace SeStep\Commentable\Model;


use Kdyby\Doctrine\Entities\Attributes\Identifier;
use SeStep\Model\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="commentable__comment_thread")
 *
 */
class CommentThread extends BaseEntity
{
    use Identifier;
}
