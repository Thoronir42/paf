<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Presenters;

use PAF\Common\BasePresenter;
use PAF\Common\Feed\Components\Comment\CommentFeedControl;
use PAF\Common\Feed\Components\FeedControl\FeedControlFactory;
use PAF\Common\Forms\Form;
use PAF\Common\Forms\FormFactory;
use PAF\Common\Lean\LeanSnapshots;
use PAF\Modules\CommissionModule\Components\CommissionsGrid\CommissionsGridFactory;
use PAF\Modules\CommissionModule\Components\CommissionStatus\CommissionStatusControlFactory;
use PAF\Modules\CommissionModule\Components\CommissionForm\CommissionFormFactory;
use PAF\Modules\CommissionModule\Components\CommissionForm\CommissionForm;
use PAF\Modules\CommissionModule\Facade\CommissionService;
use PAF\Modules\CommissionModule\Facade\ProductService;
use PAF\Modules\CommissionModule\Model\Commission;
use Nette\Application\BadRequestException;
use PAF\Modules\CommonModule\Components\CommentsControl\CommentsControl;
use PAF\Modules\CommonModule\Components\CommentsControl\CommentsControlFactory;
use PAF\Modules\CommonModule\Model\Comment;
use PAF\Modules\CommonModule\Presenters\Traits\DashboardComponent;
use PAF\Modules\CommonModule\Services\CommentsService;

final class CommissionPresenter extends BasePresenter
{
    use DashboardComponent;

    /** @var CommissionService @inject */
    public $commissionService;

    /** @var CommissionsGridFactory @inject */
    public $commissionsGridFactory;
    /** @var FormFactory @inject */
    public $formFactory;

    /** @var CommissionFormFactory @inject */
    public $commissionFormFactory;
    /** @var CommissionStatusControlFactory @inject */
    public $commissionStatusControlFactory;

    /** @var CommentsControlFactory @inject */
    public $commentsControlFactory;
    /** @var FeedControlFactory @inject */
    public $feedControlFactory;

    /** @var CommentsService @inject */
    public $commentsService;

    /** @var ProductService @inject */
    public $productService;

    /** @var LeanSnapshots @inject */
    public $snapshots;

    // variables
    /** @var string @persistent */
    public $archivedFilter;
    /** @var Commission */
    private $varCommission;


    /**
     * @authorize manage-commissions
     */
    public function actionList()
    {
        $grid = $this->commissionsGridFactory->create();

        $filter = null;
        if ($this->archivedFilter) {
            if ($this->archivedFilter == 'archived') {
                $filter = ['!archivedOn' => null];
            } elseif ($this->archivedFilter == 'unarchived') {
                $filter = ['archivedOn' => null];
            } else {
                $this->archivedFilter = null;
                $this->redirect('this');
            }
        }

        $grid->setDataSource($this->commissionService->getCommissionsDataSource($filter));
        $grid->addAction('edit', 'generic.edit', 'detail');

        $this['commissions'] = $grid;

        /** @var Form $filter */
        $filter = $this['commissionsFilter'];
        $filter->setDefaults([
            'archived' => $this->archivedFilter,
        ]);
    }

    /**
     * @authorize manage-commissions
     *
     * @param string $id
     */
    public function actionDetail($id)
    {
        $this->varCommission = $commission = $this->commissionService->find($id);
        if (!$commission) {
            throw new BadRequestException('commission-not-found');
        }

        $this->snapshots->store($commission);

        $feedControl = $this->feedControlFactory->create($this->commissionService->getCommissionFeed($commission));

        $this['feed'] = $feedControl;


        /** @var CommissionForm $form */
        $form = $this['commissionForm'];
        $form->setEntity($commission);

        /** @var CommentsControl $commentControl */
        $commentControl = $this['comments'];

        $commentControl->onCommentAdd[] = function (Comment $comment) use ($commission) {
            $comment->thread = $commission->comments;
            $comment->setUserId($this->user->identity->getEntity()->id);

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

        $stateControl = $this->commissionStatusControlFactory->create($commission);
        $stateControl->onAction[] = function (string $action) use ($commission) {
            $result = $this->commissionService->executeAction($commission, $action);
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

        $stateControl->onArchivedChanged[] = function (bool $archived) use ($commission) {
            if (!$this->commissionService->setArchived($commission, $archived)) {
                $this->flashTranslate('generic.operationFailed');
            } else {
                $this->flashTranslate('generic.success');
            }

            $this->redirect('this');
        };

        $this['stateControl'] = $stateControl;

        $this->template->productExists = $this->productService->productExists($commission->slug);
        $this->template->commission = $this->varCommission;
    }

    public function createComponentComments()
    {
        return $this->commentsControlFactory->create();
    }

    public function createComponentCommissionForm()
    {
        $form = $this->commissionFormFactory->create();
        $form->onSave[] = function (Commission $commission) {
            $this->commissionService->save($commission);
            $this->flashTranslate('paf.commission.updated', [
                'name' => $commission->specification->characterName,
            ]);
            $this->redirect('this');
        };

        return $form;
    }

    public function createComponentCommissionsFilter()
    {
        $form = $this->formFactory->create();
        $form->setMethod('GET');
        $form->addSelect('archived', 'commission.commissions.archivedFilter', [
            null => 'generic.any',
            'archived' => 'commission.commission.status.archived',
            'unarchived' => 'commission.commission.status.unarchived',
        ]);
        $form->addSubmit('filter', 'generic.action.filter');

        $form->onSuccess[] = function ($form, $values) {
            $this->archivedFilter = $values['archived'];
            $this->redirect('this');
        };
        return $form;
    }

    public function handleCreateProduct()
    {
        $error = $this->productService->createFromCommission($this->varCommission);
        if ($error) {
            $this->flashTranslate($error);
            $this->redirect('this');
        }

        $this->redirect(':Commission:Product:view', $this->varCommission->slug->id);
    }
}
