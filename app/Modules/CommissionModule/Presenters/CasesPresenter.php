<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Presenters;

use PAF\Common\BasePresenter;
use PAF\Modules\CommissionModule\Components\CasesControl\CasesControl;
use PAF\Modules\CommissionModule\Components\PafCaseForm\PafCaseFormFactory;
use PAF\Modules\CommissionModule\Components\PafCaseForm\PafCaseForm;
use PAF\Modules\CommissionModule\Facade\PafEntities;
use PAF\Modules\CommissionModule\Model\PafCase;
use PAF\Modules\CommissionModule\Repository\PafCaseRepository;
use PAF\Modules\CommissionModule\Components\QuotesControl\QuotesControl;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommissionModule\Repository\QuoteRepository;
use Nette\Application\BadRequestException;
use SeStep\Commentable\Control\CommentsControl;
use SeStep\Commentable\Control\CommentsControlFactory;
use SeStep\Commentable\Lean\Model\Comment;
use SeStep\Commentable\Lean\Model\CommentThread;
use SeStep\Commentable\Service\CommentsService;

final class CasesPresenter extends BasePresenter
{
    /** @var QuoteRepository @inject */
    public $quotes;
    /** @var PafCaseRepository @inject */
    public $cases;
    /** @var PafEntities @inject */
    public $pafEntities;

    /** @var PafCaseFormFactory @inject */
    public $caseFormFactory;

    /** @var CommentsControlFactory @inject */
    public $commentsControlFactory;

    /** @var CommentsService @inject */
    public $commentsService;

    public function actionList()
    {
        $quotes = $this->quotes->findForOverview();
        /** @var QuotesControl $quotesComponent */
        $quotesComponent = $this['quotes'];
        $quotesComponent->setQuotes($quotes);

        $cases = $this->cases->getCasesByStatus([PafCase::STATUS_ACCEPTED, PafCase::STATUS_WIP]);
        /** @var CasesControl $casesComponent */
        $casesComponent = $this['cases'];
        $casesComponent->setCases($cases);
    }

    public function actionDetail($id)
    {
        $this->template->case = $case = $this->cases->find($id);
        if (!$case) {
            throw new BadRequestException('case-not-found');
        }


        $thread = $case->comments;

        $comments = [];
//        $comments = $this->commentsService->findComments()
//            ->byThread($thread)
//            ->orderByDateCreated()
//            ->fetchAll();

        /** @var PafCaseForm $form */
        $form = $this['caseForm'];
        $form->setEntity($case);

        /** @var CommentsControl $commentControl */
        $commentControl = $this['comments'];
        $commentControl->setComments($thread, $comments);

        $this->template->notesCount = count($comments);
    }

    public function createComponentCases()
    {
        $casesComponent = new CasesControl();

        return $casesComponent;
    }

    public function createComponentQuotes()
    {
        $quotesComponent = new QuotesControl();

        $this->context->callInjects($quotesComponent);

        $quotesComponent->onAccept[] = function (Quote $quote) {
            $error = $this->pafEntities->acceptQuote($quote);

            if (!$error) {
                $this->flashTranslate('paf.case.created', ['name' => $quote->getFeName()]);
            } else {
                $this->flashTranslate("paf.case.$error", ['name' => $quote->getFeName()]);
            }

            $this->redirect('list');
        };

        $quotesComponent->onReject[] = function (Quote $quote) {
            $this->pafEntities->rejectQuote($quote);
            $this->flashTranslate('paf.quote.rejected', ['name' => $quote->getFeName()]);

            $this->redirect('list');
        };

        return $quotesComponent;
    }

    public function createComponentComments()
    {
        $comments = $this->commentsControlFactory->create();

        $comments->onCommentAdd[] = function (Comment $comment, CommentThread $thread) {
            $thread->addComment($comment);

            $this->em->persist($comment);
            $this->em->persist($thread);

            $this->em->flush();

            $this->redirect('this');
        };

        $comments->onCommentRemove[] = function (Comment $comment, CommentThread $thread) {
            $this->em->remove($comment);
            $this->em->flush();

            $this->flashTranslate('comments.comment.removed');

            $this->redirect('this');
        };

        return $comments;
    }

    public function createComponentCaseForm()
    {
        $form = $this->caseFormFactory->create();
        $form->onSave[] = function (PafCase $case) {
            $this->cases->persist($case);
            $this->flashTranslate('paf.case.updated', ['name' => $case->specification->characterName]);
            $this->redirect('this');
        };

        return $form;
    }
}
