<?php declare(strict_types=1);

namespace PAF\Modules\CommonModule\Components\CommentsControl;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use PAF\Common\Forms\FormFactory;
use PAF\Modules\CommonModule\Model\Comment;
use SeStep\Moment\HasMomentProvider;
use SeStep\Moment\MomentProvider;

/**
 * Class CommentsControl
 *
 * @method onCommentAdd(Comment $comment)
 */
class CommentsControl extends Control
{
    use HasMomentProvider;

    public $onCommentAdd = [];

    /** @var FormFactory */
    private $formFactory;

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
}
