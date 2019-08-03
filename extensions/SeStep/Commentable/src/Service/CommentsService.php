<?php declare(strict_types=1);
namespace SeStep\Commentable\Service;

use SeStep\Commentable\Lean\Repository\CommentRepository;
use SeStep\Commentable\Lean\Repository\CommentThreadRepository;
use SeStep\Commentable\Query\FindCommentsQuery;

class CommentsService
{
    /** @var CommentRepository */
    private $commentRepository;
    /** @var CommentThreadRepository */
    private $commentThreadRepository;

    public function __construct(CommentRepository $commentRepository, CommentThreadRepository $commentThreadRepository)
    {

        $this->commentRepository = $commentRepository;
        $this->commentThreadRepository = $commentThreadRepository;
    }
    
    public function findComments(): FindCommentsQuery
    {
        return new FindCommentsQuery($this->commentRepository);
    }
}
