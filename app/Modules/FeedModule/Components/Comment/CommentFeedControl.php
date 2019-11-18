<?php declare(strict_types=1);

namespace PAF\Modules\FeedModule\Components\Comment;

use PAF\Modules\FeedModule\Components\FeedControl\FeedEntryControl;
use PAF\Modules\FeedModule\FeedEvents;
use SeStep\Commentable\Lean\Model\Comment;

class CommentFeedControl extends FeedEntryControl
{
    const EVENT_DELETE = 'delete';

    /** @var Comment */
    private $comment;

    public function __construct(FeedEvents $events, Comment $comment)
    {
        parent::__construct($events);
        $this->comment = $comment;
    }

    /**
     * Renders current entry for feed
     *
     * @return void
     */
    public function renderFeed(): void
    {
        $template = $this->createTemplate();
        $template->comment = $this->comment;

        $template->setFile(__DIR__ . '/commentFeedControl.latte');

        $template->render();
    }

    public function handleDelete()
    {
        $this->events->fire(self::class, self::EVENT_DELETE, $this->comment);
    }
}
