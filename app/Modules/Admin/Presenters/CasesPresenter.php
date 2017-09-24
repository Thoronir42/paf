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
use Nette\Application\BadRequestException;

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
        $this->template->case = $this->case = $this->cases->getByName($name);
        if (!$this->case) {
            throw new BadRequestException('case-not-found');
        }

        /** @var PafCaseForm $form */
        $form = $this['caseForm'];

        $form->setEntity($this->case);
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
        $caseControl = new PafCaseControl($this->case);
        $caseControl->onUpdate[] = function (PafCase $case) {
            dump($case);
            exit;
        };

        return $caseControl;
    }

    public function createComponentCaseForm() {
        $form = $this->caseFormFactory->create();
        $form->onSave[] = function(PafCase $case) {
            dump($case); exit;
        };

        return $form;
    }
}
