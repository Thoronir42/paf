<?php declare(strict_types=1);

namespace SeStep\Commentable\Control;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use PAF\Common\Forms\FormFactory;
use PAF\Utils\Moment\HasMomentProvider;
use PAF\Utils\Moment\MomentProvider;
use SeStep\Commentable\Lean\Model\Comment;
use SeStep\Commentable\Lean\Model\CommentThread;
use UnexpectedValueException;

/**
 * Class CommentsControl
 * @package SeStep\Commentable\Control
 *
 * @method onCommentAdd(Comment $comment)
 * @method onCommentRemove(Comment $comment)
 */
class CommentsControl extends Control
{
    use HasMomentProvider;

    public $onCommentAdd = [];

    public $onCommentRemove = [];

    /** @var FormFactory */
    private $formFactory;

    /** @var Comment[] */
    private $comments = [];

    public function __construct(FormFactory $formFactory, MomentProvider $momentProvider)
    {
        $this->formFactory = $formFactory;
        $this->momentProvider = $momentProvider;
    }

    public function renderInput($withLabels = true)
    {
        $template = $this->createTemplate();

        $template->setFile(__DIR__ . '/commentsInput.latte');
        $template->renderLabels = $withLabels;

        $template->render();
    }

    public function renderStack()
    {
        $template = $this->createTemplate();

        $template->comments = $this->comments;

        $template->setFile(__DIR__ . '/commentsStack.latte');

        $template->render();
    }

    /**
     * @param Comment[] $comments
     */
    public function setComments(array $comments)
    {
        foreach ($comments as $comment) {
            if (!($comment instanceof Comment)) {
                throw new UnexpectedValueException("Comments array contained non-comment item: " . gettype($comment));
            }
        }

        $this->comments = $comments;
    }

    public function createComponentFormInput()
    {
        $form = $this->formFactory->create();

        $form->addTextArea('text', 'comments.comment.text')
            ->addRule($form::MIN_LENGTH, null, 1);
        $form->addSubmit('submit', 'generic.submit');

        $form->onSuccess[] = [$this, 'processFormInput'];

        return $form;
    }

    public function processFormInput(Form $form, $values)
    {
        $comment = new Comment();
        $comment->createdOn = $this->momentProvider->now();
        $comment->text = $values['text'];

        $this->onCommentAdd($comment);
    }

    public function handleDelete($id)
    {
        $found = null;
        foreach ($this->comments as $comment) {
            if ($comment->getId() == $id) {
                $found = $comment;
                break;
            }
        }
        if (!$found) {
            $this->flashMessage('comment_not_found', 'warning');
            return;
        }

        $this->onCommentRemove($found);
    }
}
