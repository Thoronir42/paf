<?php declare(strict_types=1);

namespace SeStep\Commentable\Service;

use SeStep\Commentable\Lean\Model\Comment;
use SeStep\Commentable\Lean\Model\CommentThread;
use SeStep\Commentable\Lean\Repository\CommentRepository;
use SeStep\Commentable\Lean\Repository\CommentThreadRepository;
use SeStep\Commentable\Lean\Query\FindCommentsQuery;

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

    public function createNewThread(): CommentThread
    {
        $thread = new CommentThread();
        $this->commentThreadRepository->persist($thread);

        return $thread;
    }

    public function findComments(): FindCommentsQuery
    {
        $findQuery = new FindCommentsQuery($this->commentRepository);
        $findQuery->setQueryable($this->commentRepository);
        return $findQuery;
    }

    public function save(Comment $comment)
    {
        return $this->commentRepository->persist($comment);
    }

    public function delete(Comment $comment)
    {
        return $this->commentRepository->delete($comment);
    }
}
