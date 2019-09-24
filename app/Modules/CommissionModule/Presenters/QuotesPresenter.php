<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Presenters;

use Nette\Application\UI\Multiplier;
use PAF\Common\BasePresenter;
use PAF\Common\Storage\PafImageStorage;
use PAF\Modules\CommissionModule\Components\QuoteForm\QuoteForm;
use PAF\Modules\CommissionModule\Facade\Commissions;
use PAF\Modules\CommissionModule\Facade\PafEntities;
use PAF\Modules\CommissionModule\Model\Specification;
use PAF\Modules\PortfolioModule\Repository\FursuitRepository;
use PAF\Modules\CommissionModule\Components\QuoteForm\QuoteFormFactory;
use PAF\Modules\CommissionModule\Components\QuoteView\QuoteView;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\CommissionModule\Repository\QuoteRepository;
use SeStep\FileAttachable\Files;

/**
 * Class QuotesPresenter
 * @package PAF\Modules\CommissionModule\Presenters
 */
final class QuotesPresenter extends BasePresenter
{
    /** @var QuoteRepository @inject */
    public $quotes;

    /** @var QuoteFormFactory @inject */
    public $quoteFormFactory;

    /** @var PafEntities @inject */
    public $pafEntities;
    /** @var Commissions @inject */
    public $commissions;

    /** @var FursuitRepository @inject */
    public $fursuits;

    /** @var PafImageStorage @inject */
    public $pafImages;

    /** @var Files @inject */
    public $files;

    public function startup()
    {
        parent::startup();
    }

    public function actionDefault()
    {
        $this->template->enableQuotes = $this->settings->getValue('paf.quotes.enable');
    }

    public function actionList()
    {
        $this->template->quotes = $this->quotes->findForOverview();
    }

    public function createComponentQuote()
    {
        return new Multiplier(function ($name) {
            $quoteView = new QuoteView($this->template->quotes[$name]);
            $quoteView->onAccept[] = function (Quote $quote) {
                $this->commissions->acceptQuote($quote);
                $this->flashTranslate('commission.quote.accepted', [
                    'name' => $quote->specification->characterName,
                ]);
                $this->redirect('this');
            };
            $quoteView->onReject[] = function (Quote $quote) {
                $this->commissions->rejectQuote($quote);
                $this->flashTranslate("commission.quote.rejected", [
                    'name' => $quote->specification->characterName,
                ]);
                $this->redirect('this');
            };
            return $quoteView;
        });
    }

    public function createComponentQuoteForm()
    {
        $form = $this->quoteFormFactory->create();

        $form->onSave[] = function (
            Quote $quote,
            Specification $specification,
            array $contacts,
            $references,
            QuoteForm $form
        ) {
            $issuer = $this->commissions->createIssuerByContacts($contacts);

            $result = $this->commissions->createNewQuote($quote, $specification, $issuer, $references);
            if (is_string($result)) {
                $form->addError($result);
                return;
            }

            $this->flashTranslate('paf.quote.created');
            $this->redirect(':Common:Homepage:default');
        };

        return $form;
    }
}
