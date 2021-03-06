<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Components\Feed;

use SeStep\NetteFeed\Components\FeedControl\FeedEntryControl;
use SeStep\NetteFeed\FeedEvents;
use PAF\Modules\CommonModule\Model\Comment;

class CommentFeedControl extends FeedEntryControl
{
    const EVENT_DELETE = 'delete';

    private Comment $comment;

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
