<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Presenters;

use PAF\Common\BasePresenter;
use PAF\Common\Feed\Components\Comment\CommentFeedControl;
use PAF\Common\Feed\Components\FeedControl\FeedControlFactory;
use PAF\Common\Lean\LeanSnapshots;
use PAF\Modules\CommissionModule\Components\CasesGrid\CasesGridFactory;
use PAF\Modules\CommissionModule\Components\CaseState\CaseStateControlFactory;
use PAF\Modules\CommissionModule\Components\PafCaseForm\PafCaseFormFactory;
use PAF\Modules\CommissionModule\Components\PafCaseForm\PafCaseForm;
use PAF\Modules\CommissionModule\Facade\PafCases;
use PAF\Modules\CommissionModule\Model\PafCase;
use Nette\Application\BadRequestException;
use SeStep\Commentable\Control\CommentsControl;
use SeStep\Commentable\Control\CommentsControlFactory;
use SeStep\Commentable\Lean\Model\Comment;
use SeStep\Commentable\Service\CommentsService;

final class CasesPresenter extends BasePresenter
{
    /** @var PafCases @inject */
    public $cases;

    /** @var CasesGridFactory @inject */
    public $casesGridFactory;

    /** @var PafCaseFormFactory @inject */
    public $caseFormFactory;
    /** @var CaseStateControlFactory @inject */
    public $caseStateControlFactory;

    /** @var CommentsControlFactory @inject */
    public $commentsControlFactory;
    /** @var FeedControlFactory @inject */
    public $feedControlFactory;

    /** @var CommentsService @inject */
    public $commentsService;

    /** @var LeanSnapshots @inject */
    public $snapshots;

    public function actionList(string $filter = null)
    {
        $casesGrid = $this->casesGridFactory->create();

        $casesGrid->setDataSource($this->cases->getCasesDataSource());
        $casesGrid->addAction('edit', 'generic.edit', 'detail');

        $this['cases'] = $casesGrid;
    }

    public function actionDetail($id)
    {
        $this->template->case = $case = $this->cases->find($id);
        if (!$case) {
            throw new BadRequestException('case-not-found');
        }

        $this->snapshots->store($case);

        $feedControl = $this->feedControlFactory->create($this->cases->getCaseFeed($case));

        $this['feed'] = $feedControl;


        /** @var PafCaseForm $form */
        $form = $this['caseForm'];
        $form->setEntity($case);

        /** @var CommentsControl $commentControl */
        $commentControl = $this['comments'];

        $commentControl->onCommentAdd[] = function (Comment $comment) use ($case) {
            $comment->thread = $case->comments;
            $comment->user = $this->user->identity->getEntity();

            $this->commentsService->save($comment);

            $this->redirect('this');
        };

        $feedControl->addEvent(
            CommentFeedControl::class,
            CommentFeedControl::EVENT_DELETE,
            function (Comment $comment) {
                $this->commentsService->delete($comment);

                $this->flashTranslate('comments.comment.removed');

                $this->redirect('this');
            }
        );

        $stateControl = $this->caseStateControlFactory->create($case);
        $stateControl->onAction[] = function (string $action) use ($case) {
            $result = $this->cases->executeAction($case, $action);
            if (!$result) {
                $message = $result->getMessage() ?: 'generic.error';
                $params = $result->getParams();

                $this->flashTranslate($message, $params, 'error');
            } else {
                $message = $result->getMessage() ?: 'generic.success';
                $params = $result->getParams();

                $this->flashTranslate($message, $params);
                $this->redirect('this');
            }
        };

        $this['stateControl'] = $stateControl;
    }

    public function createComponentComments()
    {
        return $this->commentsControlFactory->create();
    }

    public function createComponentCaseForm()
    {
        $form = $this->caseFormFactory->create();
        $form->onSave[] = function (PafCase $case) {
            $this->cases->save($case);
            $this->flashTranslate('paf.case.updated', ['name' => $case->specification->characterName]);
            $this->redirect('this');
        };

        return $form;
    }
}
