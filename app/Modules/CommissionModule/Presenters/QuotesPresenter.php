<?php declare(strict_types=1);

namespace PAF\Modules\CommissionModule\Presenters;

use Nette\Application\UI\Multiplier;
use Nette\Forms\Controls\HiddenField;
use Nette\Forms\Controls\SelectBox;
use PAF\Common\BasePresenter;
use PAF\Common\Storage\PafImageStorage;
use PAF\Modules\CommissionModule\Facade\QuoteService;
use PAF\Modules\CommissionModule\Model\Specification;
use PAF\Modules\CommonModule\Presenters\Traits\DashboardComponent;
use PAF\Modules\CommonModule\Services\FilesService;
use PAF\Modules\CommissionModule\Components\QuoteForm\QuoteFormFactory;
use PAF\Modules\CommissionModule\Components\QuoteView\QuoteView;
use PAF\Modules\CommissionModule\Model\Quote;
use PAF\Modules\DirectoryModule\Services\HasAppUser;
use PAF\Modules\DirectoryModule\Services\PersonService;

/**
 * Class QuotesPresenter
 * @package PAF\Modules\CommissionModule\Presenters
 */
final class QuotesPresenter extends BasePresenter
{
    use DashboardComponent;
    use HasAppUser;

    /** @var QuoteService @inject */
    public $quotes;

    /** @var QuoteFormFactory @inject */
    public $quoteFormFactory;

    /** @var PersonService @inject */
    public $personService;

    /** @var PafImageStorage @inject */
    public $pafImages;

    /** @var FilesService @inject */
    public $files;

    public function actionCreate()
    {
        $this->template->enableQuotes = $this->settings->getValue('commission.quotes.enable');
    }

    /**
     * @authorize manage-commissions
     */
    public function actionList()
    {
        $this->template->quotes = $this->quotes->findForOverview($this->dirPerson);
    }

    public function createComponentQuote()
    {
        return new Multiplier(function ($name) {
            $quoteView = new QuoteView($this->template->quotes[$name]);
            $quoteView->onAccept[] = function (Quote $quote) {
                $this->quotes->acceptQuote($quote);
                $this->flashTranslate('commission.quote.accepted', [
                    'name' => $quote->specification->characterName,
                ]);
                $this->redirect('this');
            };
            $quoteView->onReject[] = function (Quote $quote) {
                $this->quotes->rejectQuote($quote);
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
            string $supplier,
            Specification $specification,
            array $contacts,
            $references
        ) use ($form) {
            $supplier = $this->personService->findOne($supplier);

            if (!$supplier) {
                $supplierField = $form['quote']['supplier'];
                if ($supplierField instanceof HiddenField) {
                    $form->addError('messages.unexpected-error');
                } else {
                    /** @var SelectBox $supplierField */
                    $supplierField->addError('messages.invalidValue');
                }

                return;
            }

            $result = $this->quotes->submitNewQuote($supplier, $specification, $contacts, $references);
            if (is_string($result)) {
                $form->addError($result);
                return;
            }

            $this->flashTranslate('commission.quote.created');
            $this->redirect(':Common:Homepage:default');
        };

        return $form;
    }
}
