<?php declare(strict_types=1);

namespace PAF\Modules\QuoteModule\Presenters;


use Nette\Application\UI\Multiplier;
use Nette\Forms\Form;
use Nette\Http\FileUpload;
use PAF\Common\BasePresenter;
use PAF\Common\Storage\PafImageStorage;
use PAF\Modules\CommissionModule\Facade\PafEntities;
use PAF\Modules\PortfolioModule\Repository\FursuitRepository;
use PAF\Modules\QuoteModule\Components\QuoteForm\IQuoteFormFactory;
use PAF\Modules\QuoteModule\Components\QuoteView\QuoteView;
use PAF\Modules\QuoteModule\Model\Quote;
use PAF\Modules\QuoteModule\Repository\QuoteRepository;
use SeStep\FileAttachable\Files;

final class QuotesPresenter extends BasePresenter
{
    /** @var QuoteRepository @inject */
    public $quotes;

    /** @var IQuoteFormFactory @inject */
    public $quoteFormFactory;

    /** @var PafEntities @inject */
    public $pafEntities;

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
        $this->template->enableQuotes = $this->settings->getValue('paf.quotes.enable_quotes');

    }

    public function actionList()
    {
        $this->template->quotes = $this->quotes->findForOverview();
    }

    public function createComponentQuote()
    {
        return new Multiplier(function ($name) {
            $quoteView = new QuoteView($this->template->quotes[$name]);
            $quoteView->onAccept[] = function(Quote $quote) {
                dump('accept', $quote);exit;
            };
            $quoteView->onReject[] = function(Quote $quote) {
                dump('reject', $quote);exit;
            };
            return $quoteView;
        });
    }

    public function createComponentQuoteForm()
    {
        $form = $this->quoteFormFactory->create();

        $form->onSave[] = function (Quote $quote, Form $form, $references) {
            $entity = $this->pafEntities->findByName($quote->getSlug());
            if ($entity) {
                $entityProgress = $this->translator->translate('paf.entity.' . $entity->getMaxProgress());
                $errorVariables = [
                    'name' => $quote->getSlug(),
                    'progress' => $entityProgress,
                ];
                $errorMessage = $this->translator->translate('paf.entity.already-exists', $errorVariables);
                $form['fursuit']['name']->addError($errorMessage);
            }

            $refs = $this->files->createThread(true);
            $quote->references = $refs;

            /**@var FileUpload[] $references */
            foreach ($references as $file) {
                $fileEntity = $this->pafImages->saveQuoteReference($quote, $file, $quote->getSlug());
                $refs->addFile($fileEntity);
            }

            $this->pafEntities->createQuote($quote);
            $this->flashTranslate('paf.quote.created');
        };

        return $form;
    }


}
