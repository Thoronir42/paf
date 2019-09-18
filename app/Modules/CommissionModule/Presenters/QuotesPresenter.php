<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Presenters;

use Nette\Application\UI\Multiplier;
use Nette\Http\FileUpload;
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
                dump('accept', $quote);
                exit;
            };
            $quoteView->onReject[] = function (Quote $quote) {
                dump('reject', $quote);
                exit;
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

            $refs = $this->files->createThread(true);

            /**@var FileUpload[] $references */
            foreach ($references as $file) {
                $fileEntity = $this->pafImages->saveQuoteReference($quote, $file, $quote->slug);
                $refs->addFile($fileEntity);
            }

            $this->flashTranslate('paf.quote.created');
            $this->redirect(':Common:Homepage:default');
        };

        return $form;
    }
}
