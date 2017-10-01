<?php

namespace App\Modules\Admin\Presenters;


use App\Common\Model\Entity\PafCase;
use App\Common\Model\Entity\Quote;
use App\Common\Services\Doctrine\PafCases;
use App\Common\Services\Doctrine\PafEntities;
use App\Common\Services\Doctrine\Quotes;
use App\Modules\Admin\Controls\CaseControl\IPafCaseFormFactory;
use App\Modules\Admin\Controls\CaseControl\PafCaseControl;
use App\Modules\Admin\Controls\CaseControl\PafCaseForm;
use App\Modules\Admin\Controls\CasesControl\CasesControl;
use App\Modules\Admin\Controls\QuotesControl\QuotesControl;
use SeStep\Commentable\Query\FindCommentsQuery;
use Nette\Application\BadRequestException;
use SeStep\Commentable\Control\CommentsControl;
use SeStep\Commentable\Control\ICommentsControlFactory;
use SeStep\Commentable\Model\Comment;
use SeStep\Commentable\Model\CommentThread;

class CasesPresenter extends AdminPresenter
{
    /** @var Quotes @inject */
    public $quotes;
    /** @var PafCases @inject */
    public $cases;
    /** @var PafEntities @inject */
    public $pafEntities;

    /** @var IPafCaseFormFactory @inject */
    public $caseFormFactory;

    /** @var ICommentsControlFactory @inject */
    public $commentsControlFactory;

    private $case;

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

    public function actionDetail($name)
    {
        $this->template->case = $case = $this->cases->getByName($name);
        if (!$case) {
            throw new BadRequestException('case-not-found');
        }


        $thread = $case->getComments();
        if (!$thread) {
            $thread = new CommentThread();
            $case->setComments($thread);
            $this->cases->save($thread);
        }

        $comments = (new FindCommentsQuery($this->em))
            ->byThread($thread)
            ->orderByDateCreated()
            ->execute();

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

    public function createComponentCase()
    {
        $caseControl = new PafCaseControl();
        $caseControl->onUpdate[] = function (PafCase $case) {
            dump($case);
            exit;
        };

        return $caseControl;
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
            dump($case);
            exit;
        };

        return $form;
    }
}
