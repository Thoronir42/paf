<?php

namespace App\Modules\Admin\Presenters;


use App\Common\Controls\Views\QuoteView\QuoteView;
use App\Common\Model\Entity\PafCase;
use App\Common\Model\Entity\Quote;
use App\Common\Services\Doctrine\PafCases;
use App\Common\Services\Doctrine\PafEntities;
use App\Common\Services\Doctrine\Quotes;
use Nette\Application\UI\Multiplier;

class CasesPresenter extends AdminPresenter
{
    /** @var Quotes @inject */
    public $quotes;
    /** @var PafCases @inject */
    public $cases;

    /** @var PafEntities @inject */
    public $pafEntities;

    public function actionList()
    {
        $this->template->quotes = $this->quotes->findForOverview();
    }

    public function createComponentQuote()
    {
        return new Multiplier([$this, 'createQuoteView']);
    }

    public function createQuoteView($name)
    {
        $quoteView = new QuoteView($this->template->quotes[$name]);
        $quoteView->onAccept[] = function (Quote $quote) {
            $error = $this->pafEntities->acceptQuote($quote);

            if (!$error) {
                $this->flashTranslate('paf.case.created', ['name' => $quote->getFeName()]);
            } else {
                $this->flashTranslate("paf.case.$error", ['name' => $quote->getFeName()]);
            }

            $this->redirect('list');
        };

        $quoteView->onReject[] = function (Quote $quote) {
            $this->pafEntities->rejectQuote($quote);
            $this->flashTranslate('paf.quote.rejected', ['name' => $quote->getFeName()]);

            $this->redirect('list');
        };
        return $quoteView;
    }
}
