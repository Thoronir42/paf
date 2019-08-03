<?php declare(strict_types=1);

namespace SeStep\Commentable\Control;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use PAF\Common\Forms\FormFactory;
use SeStep\Commentable\Lean\Model\Comment;
use SeStep\Commentable\Lean\Model\CommentThread;
use UnexpectedValueException;

/**
 * Class CommentsControl
 * @package SeStep\Commentable\Control
 *
 * @method onCommentAdd(Comment $comment, CommentThread $thread)
 * @method onCommentRemove(Comment $comment, CommentThread $thread)
 */
class CommentsControl extends Control
{
    public $onCommentAdd = [];

    public $onCommentRemove = [];

    /** @var FormFactory */
    private $formFactory;

    /** @var CommentThread */
    private $thread;
    /** @var Comment[] */
    private $comments = [];

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function renderInput($withLabels = true) {
        $template = $this->createTemplate();

        $template->setFile(__DIR__ . '/commentsInput.latte');
        $template->renderLabels = $withLabels;

        $template->render();
    }

    public function renderStack() {
        $template = $this->createTemplate();

        $template->comments = $this->comments;

        $template->setFile(__DIR__ . '/commentsStack.latte');

        $template->render();
    }

    /**
     * @param CommentThread $thread
     * @param Comment[]     $comments
     */
    public function setComments(CommentThread $thread, $comments)
    {
        foreach ($comments as $comment) {
            if (!($comment instanceof Comment)) {
                throw new UnexpectedValueException("Comments array contained non-comment item: " . gettype($comment));
            }
        }

        $this->thread = $thread;

        $this->comments = $comments;
    }

    public function createComponentFormInput()
    {
        $form = $this->formFactory->create();

        $form->addTextArea('text', 'comments.comment.text');
        $form->addSubmit('submit', 'generic.submit');

        $form->onSuccess[] = [$this, 'processFormInput'];

        return $form;
    }

    public function processFormInput(Form $form, $values) {
        $comment = new Comment($this->thread, $values['text']);
        $this->onCommentAdd($comment, $this->thread);
    }

    public function handleDelete($id) {
        $found = null;
        foreach ($this->comments as $comment) {
            if($comment->getId() == $id) {
                $found = $comment;
                break;
            }
        }
        if(!$found) {
            $this->presenter->flashMessage('comment_not_found', 'warning');
            return;
        }

        $this->onCommentRemove($found,  $this->thread);
    }
}
